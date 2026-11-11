<?php
/**
 * READ-ONLY REPORT — how far off are M-Pesa C2B recharge timestamps?
 *
 * Why this exists
 * ---------------
 * Until v2.2.48 the timezone was applied at init.php:155, AFTER plugins were
 * loaded at line 134. system/plugin/c2b.php handles the C2B callback at include
 * time and exits, so its date() calls used PHP's default timezone instead of the
 * configured one. On a server whose PHP default is UTC, an Africa/Nairobi C2B
 * payment was recorded hours early.
 *
 * Why an external reference is needed
 * -----------------------------------
 * tbl_payment_gateway.paid_date cannot be used to detect this: it is written by
 * the same code path, so it carries the SAME offset and agrees with the recharge
 * row. Comparing them proves nothing.
 *
 * The only trustworthy reference is TransTime, taken straight from the Safaricom
 * C2B payload and stored in tbl_mpesa_transactions. It is East Africa Time and
 * is unaffected by anything the application does.
 *
 * This script only ever runs SELECTs. It writes nothing and changes nothing.
 *
 * Run it on the server:
 *   cd /var/www/html/isp/system && php report_c2b_timezone.php
 *
 * A new file under system/ is refused by system/.htaccess, so this is not
 * reachable over the web — CLI only, by design.
 */

include __DIR__ . '/../init.php';

function report_line($char = '-', $width = 74)
{
    echo str_repeat($char, $width), PHP_EOL;
}

report_line('=');
echo "M-PESA C2B RECHARGE TIMESTAMP REPORT (read-only)\n";
echo "Generated: " . date('Y-m-d H:i:s') . "  |  PHP timezone now: " . date_default_timezone_get() . "\n";
echo "Panel timezone setting: " . (isset($config['timezone']) ? $config['timezone'] : '(not set)') . "\n";
report_line('=');

try {
    $db = ORM::get_db();

    $sql = "
        SELECT ur.id,
               ur.username,
               ur.recharged_on,
               ur.recharged_time,
               ur.expiration,
               ur.time AS expiry_time,
               ur.method,
               mt.TransTime
        FROM tbl_user_recharges ur
        JOIN tbl_mpesa_transactions mt
          ON mt.TransID = SUBSTRING_INDEX(ur.method, ' - ', -1)
        WHERE ur.method LIKE '%C2B%'
        ORDER BY ur.id DESC
    ";

    $rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    echo "Query failed: " . $e->getMessage() . PHP_EOL;
    echo "If the join returned an error, check that tbl_mpesa_transactions exists.\n";
    exit(1);
}

if (!$rows) {
    echo "No C2B recharges matched.\n\n";
    echo "That usually means the join found nothing. Check the format actually stored\n";
    echo "in tbl_user_recharges.method with:\n\n";
    echo "  SELECT method, COUNT(*) FROM tbl_user_recharges\n";
    echo "  GROUP BY method DESC LIMIT 10;\n\n";
    exit(0);
}

$buckets   = [];
$usable    = 0;
$skipped   = 0;
$samples   = [];

foreach ($rows as $r) {
    $raw = trim((string) $r['TransTime']);

    // Safaricom sends YYYYMMDDHHMMSS. Anything else we cannot compare, so skip it
    // rather than guess.
    if (!preg_match('/^\d{14}$/', $raw)) {
        $skipped++;
        continue;
    }

    $mpesaTs = DateTime::createFromFormat('YmdHis', $raw);
    $recorded = strtotime(trim($r['recharged_on'] . ' ' . $r['recharged_time']));

    if (!$mpesaTs || !$recorded) {
        $skipped++;
        continue;
    }

    // recorded minus M-Pesa, in minutes. Negative = recorded EARLIER than reality.
    $offsetMin = (int) round(($recorded - $mpesaTs->getTimestamp()) / 60);

    // Bucket to the nearest 15 minutes so a couple of seconds of drift between
    // the callback and the recharge doesn't scatter the results.
    $bucket = (int) (round($offsetMin / 15) * 15);
    $buckets[$bucket] = ($buckets[$bucket] ?? 0) + 1;
    $usable++;

    if (count($samples) < 15) {
        $samples[] = [
            'id'       => $r['id'],
            'username' => $r['username'],
            'recorded' => trim($r['recharged_on'] . ' ' . $r['recharged_time']),
            'mpesa'    => $mpesaTs->format('Y-m-d H:i:s'),
            'offset'   => $offsetMin,
        ];
    }
}

echo "Rows matched            : " . count($rows) . PHP_EOL;
echo "Comparable (valid time) : " . $usable . PHP_EOL;
echo "Skipped (odd TransTime) : " . $skipped . PHP_EOL;
report_line();

if (!$usable) {
    echo "Nothing could be compared. Inspect a TransTime value directly:\n";
    echo "  SELECT TransID, TransTime FROM tbl_mpesa_transactions LIMIT 5;\n";
    exit(0);
}

echo "OFFSET DISTRIBUTION  (recorded minus M-Pesa, in minutes)\n";
report_line();
ksort($buckets);
foreach ($buckets as $offset => $count) {
    $label = ($offset === 0) ? 'exact' : (($offset < 0) ? 'early' : 'late');
    printf("  %+6d min  %-6s  %6d row(s)\n", $offset, $label, $count);
}

$early = 0;
foreach ($buckets as $offset => $count) {
    if ($offset <= -150) {
        $early += $count;
    }
}
report_line();
echo "Rows at least 2.5 hours early: " . $early . " of " . $usable . "\n";
if ($early > 0) {
    echo "Those customers were disconnected early by roughly the same amount.\n";
}
report_line();

echo "MOST RECENT " . count($samples) . " ROWS\n";
report_line();
printf("  %-6s %-12s %-20s %-20s %s\n", 'id', 'username', 'recorded', 'mpesa (EAT)', 'offset');
report_line();
foreach ($samples as $s) {
    printf(
        "  %-6s %-12s %-20s %-20s %+d min\n",
        $s['id'],
        substr((string) $s['username'], 0, 12),
        $s['recorded'],
        $s['mpesa'],
        $s['offset']
    );
}

report_line('=');
echo "Nothing was modified. This report is read-only.\n";
