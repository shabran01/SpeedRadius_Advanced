<?php
/**
 * Duplicate Transaction Cleanup Tool
 * 
 * Removes duplicate M-Pesa transactions caused by the callback race condition bug.
 * 
 * USAGE:
 *   CLI:  php cleanup_duplicates.php            (dry-run)
 *         php cleanup_duplicates.php --execute  (delete)
 *   Web:  Open in browser for an interactive UI
 */

require_once __DIR__ . '/init.php';

// Detect CLI vs Web
$isCLI = (php_sapi_name() === 'cli' || !isset($_SERVER['HTTP_HOST']));
$isWeb = !$isCLI;

// Determine mode: dry-run or execute
if ($isCLI) {
    $execute = in_array('--execute', $argv ?? []);
} else {
    $execute = ($_POST['mode'] ?? '') === 'execute';
}
$dryRun = !$execute;

// ============================================================
// CORE LOGIC — scans for duplicates and optionally deletes
// ============================================================
function runScan($execute) {
    $result = [
        'pgGroups' => 0,
        'pgWarnings' => [],
        'trxGroups' => [],
        'urGroups' => [],
        'step2Groups' => 0,
        'step3Groups' => 0,
        'totalTrxDeleted' => 0,
        'totalURDeleted' => 0,
        'totalTrxWouldDelete' => 0,
        'totalURWouldDelete' => 0,
    ];

    // STEP 1: Payment gateway duplicates
    $dupPG = ORM::for_table('tbl_payment_gateway')
        ->select_expr('gateway_trx_id')
        ->select_expr('COUNT(*)', 'cnt')
        ->where_not_equal('gateway_trx_id', '')
        ->where_not_null('gateway_trx_id')
        ->where('status', 2)
        ->group_by('gateway_trx_id')
        ->having_raw('COUNT(*) > 1')
        ->find_many();

    $result['pgGroups'] = count($dupPG);
    foreach ($dupPG as $dup) {
        $result['pgWarnings'][] = ['code' => $dup->gateway_trx_id, 'cnt' => $dup->cnt];
    }

    // STEP 2: Transaction duplicates
    $dupTrx = ORM::for_table('tbl_transactions')
        ->select('username')
        ->select('plan_name')
        ->select('price')
        ->select('recharged_on')
        ->select_expr('COUNT(*)', 'cnt')
        ->select_expr('GROUP_CONCAT(id ORDER BY id ASC)', 'ids')
        ->select_expr('GROUP_CONCAT(invoice ORDER BY id ASC)', 'invoices')
        ->select_expr('MIN(recharged_time)', 'min_time')
        ->select_expr('MAX(recharged_time)', 'max_time')
        ->where_like('method', '%Mpesa%')
        ->group_by('username')
        ->group_by('plan_name')
        ->group_by('price')
        ->group_by('recharged_on')
        ->having_raw('COUNT(*) > 1')
        ->find_many();

    foreach ($dupTrx as $dup) {
        $ids = explode(',', $dup->ids);
        $invoices = explode(',', $dup->invoices);
        $minSecs = strtotime($dup->recharged_on . ' ' . $dup->min_time);
        $maxSecs = strtotime($dup->recharged_on . ' ' . $dup->max_time);
        if (($maxSecs - $minSecs) > 120) continue;

        $keepId = intval($ids[0]);
        $deleteIds = array_slice($ids, 1);
        $deleteInvoices = array_slice($invoices, 1);

        $group = [
            'username' => $dup->username,
            'plan' => $dup->plan_name,
            'price' => $dup->price,
            'date' => $dup->recharged_on,
            'timeMin' => $dup->min_time,
            'timeMax' => $dup->max_time,
            'keepInvoice' => $invoices[0],
            'keepId' => $keepId,
            'deleteInvoices' => $deleteInvoices,
            'deleteIds' => array_map('intval', $deleteIds),
        ];

        if ($execute) {
            $deleted = 0;
            foreach ($deleteIds as $delId) {
                $delId = intval($delId);
                if ($delId === $keepId) continue;
                $trx = ORM::for_table('tbl_transactions')->find_one($delId);
                if ($trx) { $trx->delete(); $deleted++; }
            }
            $group['deleted'] = $deleted;
            $result['totalTrxDeleted'] += $deleted;
        } else {
            $result['totalTrxWouldDelete'] += count($deleteIds);
        }

        $result['trxGroups'][] = $group;
        $result['step2Groups']++;
    }

    // STEP 3: User recharge duplicates
    $dupUR = ORM::for_table('tbl_user_recharges')
        ->select('username')
        ->select('plan_id')
        ->select('recharged_on')
        ->select_expr('COUNT(*)', 'cnt')
        ->select_expr('GROUP_CONCAT(id ORDER BY id ASC)', 'ids')
        ->select_expr('MIN(recharged_time)', 'min_time')
        ->select_expr('MAX(recharged_time)', 'max_time')
        ->where_like('method', '%Mpesa%')
        ->group_by('username')
        ->group_by('plan_id')
        ->group_by('recharged_on')
        ->having_raw('COUNT(*) > 1')
        ->find_many();

    foreach ($dupUR as $dup) {
        $ids = explode(',', $dup->ids);
        $minSecs = strtotime($dup->recharged_on . ' ' . $dup->min_time);
        $maxSecs = strtotime($dup->recharged_on . ' ' . $dup->max_time);
        if (($maxSecs - $minSecs) > 120) continue;

        $keepId = intval($ids[0]);
        $deleteIds = array_slice($ids, 1);

        $group = [
            'username' => $dup->username,
            'planId' => $dup->plan_id,
            'date' => $dup->recharged_on,
            'timeMin' => $dup->min_time,
            'timeMax' => $dup->max_time,
            'keepId' => $keepId,
            'deleteIds' => array_map('intval', $deleteIds),
        ];

        if ($execute) {
            $deleted = 0;
            foreach ($deleteIds as $delId) {
                $delId = intval($delId);
                if ($delId === $keepId) continue;
                $ur = ORM::for_table('tbl_user_recharges')->find_one($delId);
                if ($ur) { $ur->delete(); $deleted++; }
            }
            $group['deleted'] = $deleted;
            $result['totalURDeleted'] += $deleted;
        } else {
            $result['totalURWouldDelete'] += count($deleteIds);
        }

        $result['urGroups'][] = $group;
        $result['step3Groups']++;
    }

    return $result;
}

// Run the scan
$result = runScan($execute);
$totalWouldDelete = $result['totalTrxWouldDelete'] + $result['totalURWouldDelete'];
$totalDeleted = $result['totalTrxDeleted'] + $result['totalURDeleted'];

// ============================================================
// CLI OUTPUT
// ============================================================
if ($isCLI) {
    echo str_repeat('=', 60) . "\n";
    echo $dryRun ? "  DRY RUN MODE - No changes will be made\n" : "  EXECUTE MODE - Deleting duplicates now\n";
    echo str_repeat('=', 60) . "\n\n";

    echo "STEP 1: tbl_payment_gateway duplicates: {$result['pgGroups']}\n";
    foreach ($result['pgWarnings'] as $w) {
        echo "  WARNING: {$w['code']} appears {$w['cnt']} times\n";
    }
    echo "\n";

    echo "STEP 2: tbl_transactions duplicates:\n\n";
    foreach ($result['trxGroups'] as $g) {
        echo "  User: {$g['username']}, Plan: {$g['plan']}, Price: {$g['price']}\n";
        echo "    Date: {$g['date']}, Time: {$g['timeMin']} - {$g['timeMax']}\n";
        echo "    Keep:  {$g['keepInvoice']} (ID: {$g['keepId']})\n";
        echo "    Delete: " . implode(', ', $g['deleteInvoices']) . " (IDs: " . implode(', ', $g['deleteIds']) . ")\n";
    }
    echo "\n";

    echo "STEP 3: tbl_user_recharges duplicates:\n\n";
    foreach ($result['urGroups'] as $g) {
        echo "  User: {$g['username']}, Plan ID: {$g['planId']}\n";
        echo "    Date: {$g['date']}, Time: {$g['timeMin']} - {$g['timeMax']}\n";
        echo "    Keep ID: {$g['keepId']} | Delete IDs: " . implode(', ', $g['deleteIds']) . "\n";
    }

    echo "\n" . str_repeat('=', 60) . "\n  SUMMARY\n" . str_repeat('=', 60) . "\n";
    echo "  tbl_payment_gateway groups: {$result['pgGroups']}\n";
    echo "  tbl_transactions groups:    {$result['step2Groups']}\n";
    echo "  tbl_user_recharges groups:  {$result['step3Groups']}\n";
    if ($dryRun) {
        echo "\n  >> DRY RUN <<\n  Would delete: {$totalWouldDelete} records\n";
    } else {
        echo "\n  >> DELETED: {$totalDeleted} records <<\n";
    }
    echo str_repeat('=', 60) . "\n";
    exit;
}

// ============================================================
// WEB UI (Tailwind CSS)
// ============================================================
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duplicate Transaction Cleanup — SpeedRadius</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    animation: {
                        'fade-in': 'fadeIn 0.4s ease-out',
                        'slide-up': 'slideUp 0.3s ease-out',
                        'spin-slow': 'spin 1.5s linear infinite',
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
                        slideUp: { '0%': { opacity: '0', transform: 'translateY(10px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } },
                    },
                },
            },
        }
    </script>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 via-gray-50 to-slate-100 text-gray-800 font-sans antialiased">

    <!-- ===== HEADER ===== -->
    <header class="bg-white/80 backdrop-blur-xl border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-4 py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <span class="text-2xl">🧹</span> Duplicate Transaction Cleanup
                </h1>
                <p class="text-sm text-gray-500 mt-0.5">M-Pesa callback race condition duplicates</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium <?= $dryRun ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' ?>">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 <?= $dryRun ? 'bg-amber-400' : 'bg-emerald-400' ?>"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 <?= $dryRun ? 'bg-amber-500' : 'bg-emerald-500' ?>"></span>
                    </span>
                    <?= $dryRun ? 'Dry Run' : 'Execute Mode' ?>
                </span>
                <span class="text-xs text-gray-400"><?= date('Y-m-d H:i:s') ?></span>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-8 animate-fade-in">

        <!-- ===== ACTION BAR ===== -->
        <div class="mb-8 p-6 bg-white rounded-2xl shadow-lg shadow-gray-200/50 border border-gray-100">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900"><?= $dryRun ? '🔍 Scan Results' : '✅ Cleanup Results' ?></h2>
                    <p class="text-sm text-gray-500 mt-1">
                        <?php if ($dryRun): ?>
                            Review duplicates below, then click <strong>Execute Cleanup</strong> to delete them.
                        <?php else: ?>
                            Duplicates have been deleted. Review the summary below.
                        <?php endif; ?>
                    </p>
                </div>
                <div class="flex gap-3">
                    <?php if ($dryRun): ?>
                    <form method="post" onsubmit="return confirm('⚠️ This will permanently delete <?= $totalWouldDelete ?> duplicate records. Are you sure?')">
                        <input type="hidden" name="mode" value="execute">
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-all duration-200 hover:shadow-lg hover:shadow-red-500/25 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Execute Cleanup (<?= $totalWouldDelete ?> records)
                        </button>
                    </form>
                    <?php else: ?>
                    <a href="?" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition-all duration-200 hover:shadow-lg hover:shadow-indigo-500/25 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        New Dry Run
                    </a>
                    <?php endif; ?>
                    <a href="?" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-all duration-200 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Refresh
                    </a>
                </div>
            </div>
        </div>

        <!-- ===== SUMMARY CARDS ===== -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- PG Duplicates -->
            <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 border border-gray-100 p-5 hover:shadow-xl transition-all duration-300">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Payment Gateway</p>
                <p class="text-3xl font-bold <?= $result['pgGroups'] > 0 ? 'text-red-600' : 'text-gray-900' ?>"><?= $result['pgGroups'] ?></p>
                <p class="text-xs text-gray-400 mt-2">duplicate gateway_trx_id groups</p>
            </div>
            <!-- Transaction Duplicates -->
            <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 border border-gray-100 p-5 hover:shadow-xl transition-all duration-300">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Transactions</p>
                <p class="text-3xl font-bold <?= $result['step2Groups'] > 0 ? 'text-amber-600' : 'text-gray-900' ?>"><?= $result['step2Groups'] ?></p>
                <p class="text-xs text-gray-400 mt-2">duplicate invoice groups</p>
            </div>
            <!-- Recharge Duplicates -->
            <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 border border-gray-100 p-5 hover:shadow-xl transition-all duration-300">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Recharges</p>
                <p class="text-3xl font-bold <?= $result['step3Groups'] > 0 ? 'text-amber-600' : 'text-gray-900' ?>"><?= $result['step3Groups'] ?></p>
                <p class="text-xs text-gray-400 mt-2">duplicate recharge groups</p>
            </div>
            <!-- Action Count -->
            <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 border border-gray-100 p-5 hover:shadow-xl transition-all duration-300">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1"><?= $dryRun ? 'Would Delete' : 'Deleted' ?></p>
                <p class="text-3xl font-bold <?= ($dryRun ? $totalWouldDelete : $totalDeleted) > 0 ? ($dryRun ? 'text-orange-600' : 'text-emerald-600') : 'text-gray-900' ?>">
                    <?= $dryRun ? $totalWouldDelete : $totalDeleted ?>
                </p>
                <p class="text-xs text-gray-400 mt-2">total records <?= $dryRun ? 'affected' : 'removed' ?></p>
            </div>
        </div>

        <?php if ($result['pgWarnings']): ?>
        <!-- ===== PG WARNINGS ===== -->
        <div class="mb-8 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl">
            <h3 class="text-red-800 font-semibold text-sm">⚠️ Payment Gateway Duplicates Detected</h3>
            <?php foreach ($result['pgWarnings'] as $w): ?>
                <p class="text-red-600 text-sm mt-1">Code: <?= htmlspecialchars($w['code']) ?> — <?= $w['cnt'] ?> occurrences</p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- ===== TRANSACTION DUPLICATES TABLE ===== -->
        <?php if ($result['trxGroups']): ?>
        <div class="mb-8 bg-white rounded-2xl shadow-lg shadow-gray-200/50 border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">📋 Duplicate Transactions <span class="text-sm font-normal text-gray-400">(<?= $result['step2Groups'] ?> groups)</span></h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">User</th>
                            <th class="px-6 py-3">Plan</th>
                            <th class="px-6 py-3">Price</th>
                            <th class="px-6 py-3">Date / Time</th>
                            <th class="px-6 py-3">Keep</th>
                            <th class="px-6 py-3">Delete</th>
                            <th class="px-6 py-3 text-center"><?= $dryRun ? 'Would Delete' : 'Deleted' ?></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($result['trxGroups'] as $g): ?>
                        <tr class="hover:bg-gray-50/50 transition-colors animate-slide-up">
                            <td class="px-6 py-3 font-medium text-gray-900">#<?= htmlspecialchars($g['username']) ?></td>
                            <td class="px-6 py-3 text-gray-600"><?= htmlspecialchars($g['plan']) ?></td>
                            <td class="px-6 py-3 text-gray-600">Ksh. <?= $g['price'] ?></td>
                            <td class="px-6 py-3 text-gray-500">
                                <?= htmlspecialchars($g['date']) ?><br>
                                <span class="text-xs"><?= htmlspecialchars($g['timeMin']) ?> – <?= htmlspecialchars($g['timeMax']) ?></span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-medium">
                                    <?= htmlspecialchars($g['keepInvoice']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex flex-wrap gap-1">
                                    <?php foreach ($g['deleteInvoices'] as $inv): ?>
                                    <span class="inline-flex px-2 py-1 bg-red-50 text-red-600 rounded-lg text-xs font-medium"><?= htmlspecialchars($inv) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td class="px-6 py-3 text-center font-semibold <?= ($g['deleted'] ?? count($g['deleteIds'])) > 0 ? 'text-red-600' : 'text-gray-400' ?>">
                                <?= $dryRun ? count($g['deleteIds']) : ($g['deleted'] ?? 0) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php else: ?>
        <div class="mb-8 p-8 bg-emerald-50 rounded-2xl border border-emerald-200 text-center">
            <p class="text-emerald-700 font-semibold text-lg">✅ No duplicate transactions found!</p>
            <p class="text-emerald-600 text-sm mt-1">All clean.</p>
        </div>
        <?php endif; ?>

        <!-- ===== USER RECHARGE DUPLICATES TABLE ===== -->
        <?php if ($result['urGroups']): ?>
        <div class="mb-8 bg-white rounded-2xl shadow-lg shadow-gray-200/50 border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">🔌 Duplicate Recharges <span class="text-sm font-normal text-gray-400">(<?= $result['step3Groups'] ?> groups)</span></h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">User</th>
                            <th class="px-6 py-3">Plan ID</th>
                            <th class="px-6 py-3">Date / Time</th>
                            <th class="px-6 py-3">Keep ID</th>
                            <th class="px-6 py-3">Delete IDs</th>
                            <th class="px-6 py-3 text-center"><?= $dryRun ? 'Would Delete' : 'Deleted' ?></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($result['urGroups'] as $g): ?>
                        <tr class="hover:bg-gray-50/50 transition-colors animate-slide-up">
                            <td class="px-6 py-3 font-medium text-gray-900">#<?= htmlspecialchars($g['username']) ?></td>
                            <td class="px-6 py-3 text-gray-600"><?= htmlspecialchars($g['planId']) ?></td>
                            <td class="px-6 py-3 text-gray-500">
                                <?= htmlspecialchars($g['date']) ?><br>
                                <span class="text-xs"><?= htmlspecialchars($g['timeMin']) ?> – <?= htmlspecialchars($g['timeMax']) ?></span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="inline-flex px-2 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-mono font-medium"><?= $g['keepId'] ?></span>
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex flex-wrap gap-1">
                                    <?php foreach ($g['deleteIds'] as $did): ?>
                                    <span class="inline-flex px-2 py-1 bg-red-50 text-red-600 rounded-lg text-xs font-mono font-medium"><?= $did ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td class="px-6 py-3 text-center font-semibold <?= ($g['deleted'] ?? count($g['deleteIds'])) > 0 ? 'text-red-600' : 'text-gray-400' ?>">
                                <?= $dryRun ? count($g['deleteIds']) : ($g['deleted'] ?? 0) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php else: ?>
        <div class="mb-8 p-8 bg-emerald-50 rounded-2xl border border-emerald-200 text-center">
            <p class="text-emerald-700 font-semibold text-lg">✅ No duplicate recharges found!</p>
            <p class="text-emerald-600 text-sm mt-1">All clean.</p>
        </div>
        <?php endif; ?>

        <!-- ===== FOOTER ===== -->
        <footer class="text-center text-xs text-gray-400 py-6">
            <p>SpeedRadius Duplicate Cleanup Tool · Affected tables: tbl_payment_gateway, tbl_transactions, tbl_user_recharges</p>
        </footer>

    </main>
</body>
</html>