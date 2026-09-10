<?php
/*
|--------------------------------------------------------------------------
| SMS Gateway Web Panel
|--------------------------------------------------------------------------
| Configure your credentials below, then upload this folder to your
| web server. Access it via: http://yourserver/sms-panel/
|
*/

// ===== CONFIGURATION =====
$config = [
    'api_url'   => 'https://api.sms-gate.app/3rdparty/v1/messages',
    'username'  => 'DVWYEW',
    'password'  => 'rlxqfsbsezsiqh',
    'device_id' => '',  // Leave blank to auto-select device
];

// ===== CSRF TOKEN =====
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMS Panel — Send SMS</title>
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #dbeafe;
            --green: #059669;
            --green-light: #d1fae5;
            --red: #dc2626;
            --red-light: #fee2e2;
            --orange: #d97706;
            --orange-light: #fef3c7;
            --bg: #f1f5f9;
            --card: #ffffff;
            --text: #1e293b;
            --muted: #64748b;
            --border: #e2e8f0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: white;
            padding: 24px 20px;
            text-align: center;
        }
        .header h1 { font-size: 1.5rem; font-weight: 700; }
        .header p { opacity: 0.85; font-size: 0.88rem; margin-top: 4px; }

        /* Status bar */
        .status-bar {
            background: var(--card);
            border-bottom: 1px solid var(--border);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            font-size: 0.85rem;
        }
        .status-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .status-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #94a3b8;
        }
        .status-dot.online { background: var(--green); box-shadow: 0 0 6px rgba(5,150,105,0.5); }
        .status-dot.offline { background: var(--red); }

        /* Container */
        .container { max-width: 720px; margin: 0 auto; padding: 24px 16px; }

        /* Tabs */
        .tabs {
            display: flex;
            background: var(--card);
            border-radius: 12px 12px 0 0;
            border: 1px solid var(--border);
            border-bottom: none;
            overflow: hidden;
        }
        .tab {
            flex: 1;
            padding: 14px;
            text-align: center;
            font-weight: 600;
            font-size: 0.92rem;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.2s;
            background: var(--bg);
            color: var(--muted);
        }
        .tab:hover { background: #e8ecf1; }
        .tab.active {
            background: var(--card);
            color: var(--primary);
            border-bottom-color: var(--primary);
        }
        .tab-icon { margin-right: 6px; }

        /* Panel */
        .panel {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 0 0 12px 12px;
            padding: 28px;
        }
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        /* Form */
        .form-group { margin-bottom: 18px; }
        label {
            display: block;
            font-weight: 600;
            font-size: 0.88rem;
            margin-bottom: 6px;
            color: var(--text);
        }
        label .hint {
            font-weight: 400;
            color: var(--muted);
            font-size: 0.8rem;
        }
        input[type="text"],
        input[type="tel"],
        textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 0.92rem;
            font-family: inherit;
            transition: border-color 0.2s;
            background: #fafbfc;
        }
        input:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
            background: white;
        }
        textarea { resize: vertical; min-height: 100px; }
        .char-count {
            text-align: right;
            font-size: 0.78rem;
            color: var(--muted);
            margin-top: 4px;
        }
        .char-count.warn { color: var(--orange); font-weight: 600; }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 28px;
            border: none;
            border-radius: 8px;
            font-size: 0.92rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            font-family: inherit;
        }
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-primary:disabled {
            background: #94a3b8;
            cursor: not-allowed;
        }
        .btn-secondary {
            background: var(--bg);
            color: var(--text);
            border: 1px solid var(--border);
        }
        .btn-secondary:hover { background: #e2e8f0; }
        .btn-row {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        /* Number counter badge */
        .num-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: var(--primary-light);
            color: var(--primary);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.82rem;
            font-weight: 600;
        }

        /* Results */
        .result-box {
            margin-top: 20px;
            padding: 16px;
            border-radius: 10px;
            display: none;
            font-size: 0.9rem;
        }
        .result-box.success {
            background: var(--green-light);
            border: 1px solid #6ee7b7;
            color: #065f46;
            display: block;
        }
        .result-box.error {
            background: var(--red-light);
            border: 1px solid #fca5a5;
            color: #991b1b;
            display: block;
        }
        .result-box.sending {
            background: var(--primary-light);
            border: 1px solid #93c5fd;
            color: #1e40af;
            display: block;
        }

        /* Progress bar */
        .progress-wrap {
            background: #e2e8f0;
            border-radius: 8px;
            height: 8px;
            margin: 12px 0;
            overflow: hidden;
            display: none;
        }
        .progress-wrap.active { display: block; }
        .progress-bar {
            height: 100%;
            background: var(--primary);
            border-radius: 8px;
            transition: width 0.3s;
            width: 0%;
        }

        /* Bulk log */
        .bulk-log {
            max-height: 300px;
            overflow-y: auto;
            margin-top: 12px;
            font-size: 0.82rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            display: none;
        }
        .bulk-log.active { display: block; }
        .log-row {
            padding: 7px 12px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .log-row:last-child { border-bottom: none; }
        .log-row:nth-child(even) { background: #f8fafc; }
        .log-ok { color: var(--green); font-weight: 600; }
        .log-fail { color: var(--red); font-weight: 600; }
        .log-wait { color: var(--muted); }

        /* Stats bar */
        .stats {
            display: flex;
            gap: 16px;
            margin-top: 12px;
            flex-wrap: wrap;
        }
        .stat-box {
            flex: 1;
            min-width: 100px;
            background: var(--bg);
            border-radius: 8px;
            padding: 12px;
            text-align: center;
        }
        .stat-num { font-size: 1.4rem; font-weight: 800; }
        .stat-num.green { color: var(--green); }
        .stat-num.red { color: var(--red); }
        .stat-num.blue { color: var(--primary); }
        .stat-label { font-size: 0.75rem; color: var(--muted); margin-top: 2px; }

        /* Info box */
        .info-box {
            background: var(--primary-light);
            border: 1px solid #93c5fd;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 0.85rem;
            color: #1e40af;
            margin-bottom: 16px;
        }

        /* Separator */
        hr.sep { border: none; height: 1px; background: var(--border); margin: 16px 0; }

        /* Footer */
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 0.8rem;
            color: var(--muted);
        }

        /* Spinner */
        .spinner {
            display: inline-block;
            width: 16px; height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        @media (max-width: 600px) {
            .panel { padding: 18px 14px; }
            .stats { gap: 8px; }
            .stat-box { min-width: 80px; padding: 8px; }
        }
    </style>
</head>
<body>

<div class="header">
    <h1>📱 SMS Panel</h1>
    <p>Send single or bulk SMS via your Cloud Gateway</p>
</div>

<div class="status-bar">
    <div class="status-item">
        <span class="status-dot" id="serverDot"></span>
        <span id="serverStatus">Checking server...</span>
    </div>
    <div class="status-item">
        <span>🌐</span>
        <span>api.sms-gate.app</span>
    </div>
</div>

<div class="container">

    <!-- TABS -->
    <div class="tabs">
        <div class="tab active" onclick="switchTab('single')">
            <span class="tab-icon">✉️</span> Single SMS
        </div>
        <div class="tab" onclick="switchTab('bulk')">
            <span class="tab-icon">📨</span> Bulk SMS
        </div>
    </div>

    <div class="panel">

        <!-- ===== SINGLE SMS ===== -->
        <div class="tab-content active" id="tab-single">
            <form id="singleForm" onsubmit="return sendSingle(event)">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">

                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" id="singlePhone" name="phone" placeholder="+254712345678 or 0712345678" required>
                </div>

                <div class="form-group">
                    <label>Message <span class="hint">— max 10 parts (1530 chars)</span></label>
                    <textarea id="singleMsg" name="message" placeholder="Type your message here..." required></textarea>
                    <div class="char-count" id="singleCount">0 / 160 (1 SMS)</div>
                </div>

                <div class="btn-row">
                    <button type="submit" class="btn btn-primary" id="singleBtn">
                        ✉️ Send SMS
                    </button>
                </div>

                <div class="result-box" id="singleResult"></div>
            </form>
        </div>

        <!-- ===== BULK SMS ===== -->
        <div class="tab-content" id="tab-bulk">
            <div class="info-box">
                Enter one phone number per line, or paste comma-separated numbers. All will receive the same message.
            </div>

            <form id="bulkForm" onsubmit="return sendBulk(event)">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">

                <div class="form-group">
                    <label>Phone Numbers <span class="hint">— one per line or comma-separated</span></label>
                    <textarea id="bulkPhones" name="phones" rows="6" placeholder="+254712345678
+254798765432
0718167262
..." required></textarea>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:6px;">
                        <span class="num-badge" id="numCount">0 numbers</span>
                        <button type="button" class="btn btn-secondary" onclick="cleanNumbers()" style="padding:6px 14px; font-size:0.8rem;">🧹 Clean &amp; Deduplicate</button>
                    </div>
                </div>

                <div class="form-group">
                    <label>Message <span class="hint">— same for all recipients</span></label>
                    <textarea id="bulkMsg" name="message" placeholder="Type your message here..." required></textarea>
                    <div class="char-count" id="bulkCount">0 / 160 (1 SMS)</div>
                </div>

                <div class="btn-row">
                    <button type="submit" class="btn btn-primary" id="bulkBtn">
                        📨 Send Bulk SMS
                    </button>
                    <button type="button" class="btn btn-secondary" id="bulkStop" onclick="stopBulk()" style="display:none;">
                        ⛔ Stop
                    </button>
                </div>

                <!-- Progress -->
                <div class="progress-wrap" id="bulkProgress">
                    <div class="progress-bar" id="bulkBar"></div>
                </div>

                <!-- Stats -->
                <div class="stats" id="bulkStats" style="display:none;">
                    <div class="stat-box">
                        <div class="stat-num blue" id="statTotal">0</div>
                        <div class="stat-label">Total</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-num green" id="statSent">0</div>
                        <div class="stat-label">Sent</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-num red" id="statFailed">0</div>
                        <div class="stat-label">Failed</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-num" id="statPending">0</div>
                        <div class="stat-label">Pending</div>
                    </div>
                </div>

                <!-- Log -->
                <div class="bulk-log" id="bulkLog"></div>

                <div class="result-box" id="bulkResult"></div>
            </form>
        </div>

    </div>

    <div class="footer">
        SMS Gateway Panel &bull; Cloud Server
    </div>

</div>

<script>
const API_URL = 'send.php';
let bulkStopped = false;

// ===== TABS =====
function switchTab(name) {
    document.querySelectorAll('.tab').forEach((t, i) => {
        t.classList.toggle('active', (name === 'single' && i === 0) || (name === 'bulk' && i === 1));
    });
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
}

// ===== CHARACTER COUNT =====
function updateCharCount(textarea, countEl) {
    const len = textarea.value.length;
    const parts = len <= 160 ? 1 : Math.ceil(len / 153);
    const limit = parts === 1 ? 160 : 153;
    countEl.textContent = len + ' / ' + (parts === 1 ? 160 : (parts * 153)) + ' (' + parts + ' SMS' + (parts > 1 ? 'es' : '') + ')';
    countEl.className = 'char-count' + (parts > 3 ? ' warn' : '');
}
document.getElementById('singleMsg').addEventListener('input', function() {
    updateCharCount(this, document.getElementById('singleCount'));
});
document.getElementById('bulkMsg').addEventListener('input', function() {
    updateCharCount(this, document.getElementById('bulkCount'));
});

// ===== NUMBER COUNTER =====
function getNumbers(text) {
    return text.split(/[\n,;]+/)
        .map(n => n.trim().replace(/\s+/g, ''))
        .filter(n => n.length >= 9);
}

document.getElementById('bulkPhones').addEventListener('input', function() {
    const nums = getNumbers(this.value);
    document.getElementById('numCount').textContent = nums.length + ' number' + (nums.length !== 1 ? 's' : '');
});

function cleanNumbers() {
    const ta = document.getElementById('bulkPhones');
    let nums = getNumbers(ta.value);
    nums = [...new Set(nums)]; // deduplicate
    ta.value = nums.join('\n');
    document.getElementById('numCount').textContent = nums.length + ' number' + (nums.length !== 1 ? 's' : '');
}

// ===== SEND SINGLE =====
async function sendSingle(e) {
    e.preventDefault();
    const btn = document.getElementById('singleBtn');
    const result = document.getElementById('singleResult');
    const phone = document.getElementById('singlePhone').value.trim();
    const message = document.getElementById('singleMsg').value.trim();

    if (!phone || !message) return;

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span> Sending...';
    result.className = 'result-box sending';
    result.textContent = 'Sending SMS to ' + phone + '...';

    try {
        const resp = await fetch(API_URL, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                action: 'send',
                phones: [phone],
                message: message,
                csrf: '<?= $csrf ?>'
            })
        });
        const data = await resp.json();

        if (data.success) {
            result.className = 'result-box success';
            result.innerHTML = '✅ <strong>Sent!</strong> Message ID: ' + (data.message_id || 'N/A') + '<br>Phone: ' + phone;
        } else {
            result.className = 'result-box error';
            result.innerHTML = '❌ <strong>Failed!</strong> ' + (data.error || 'Unknown error');
        }
    } catch (err) {
        result.className = 'result-box error';
        result.innerHTML = '❌ <strong>Network error:</strong> ' + err.message;
    }

    btn.disabled = false;
    btn.innerHTML = '✉️ Send SMS';
}

// ===== SEND BULK =====
async function sendBulk(e) {
    e.preventDefault();
    const phones = getNumbers(document.getElementById('bulkPhones').value);
    const message = document.getElementById('bulkMsg').value.trim();

    if (phones.length === 0 || !message) return;

    bulkStopped = false;
    const btn = document.getElementById('bulkBtn');
    const stopBtn = document.getElementById('bulkStop');
    const progress = document.getElementById('bulkProgress');
    const bar = document.getElementById('bulkBar');
    const statsEl = document.getElementById('bulkStats');
    const log = document.getElementById('bulkLog');
    const result = document.getElementById('bulkResult');

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span> Sending...';
    stopBtn.style.display = 'inline-flex';
    progress.classList.add('active');
    statsEl.style.display = 'flex';
    log.classList.add('active');
    log.innerHTML = '';
    result.className = 'result-box';
    result.style.display = 'none';

    let sent = 0, failed = 0, total = phones.length;
    document.getElementById('statTotal').textContent = total;
    document.getElementById('statSent').textContent = 0;
    document.getElementById('statFailed').textContent = 0;
    document.getElementById('statPending').textContent = total;

    for (let i = 0; i < phones.length; i++) {
        if (bulkStopped) break;

        const phone = phones[i];
        addLogRow(phone, 'sending...');

        try {
            const resp = await fetch(API_URL, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    action: 'send',
                    phones: [phone],
                    message: message,
                    csrf: '<?= $csrf ?>'
                })
            });
            const data = await resp.json();

            if (data.success) {
                sent++;
                updateLogRow(phone, '✅ Sent');
            } else {
                failed++;
                updateLogRow(phone, '❌ ' + (data.error || 'Failed'));
            }
        } catch (err) {
            failed++;
            updateLogRow(phone, '❌ ' + err.message);
        }

        // Update stats
        document.getElementById('statSent').textContent = sent;
        document.getElementById('statFailed').textContent = failed;
        document.getElementById('statPending').textContent = total - sent - failed;
        bar.style.width = ((sent + failed) / total * 100) + '%';
    }

    // Done
    btn.disabled = false;
    btn.innerHTML = '📨 Send Bulk SMS';
    stopBtn.style.display = 'none';

    result.className = 'result-box ' + (failed === 0 ? 'success' : (sent === 0 ? 'error' : 'success'));
    result.innerHTML = bulkStopped
        ? '⛔ <strong>Stopped.</strong> Sent: ' + sent + ' / Failed: ' + failed + ' / Remaining: ' + (total - sent - failed)
        : '✅ <strong>Complete!</strong> Sent: ' + sent + ' / Failed: ' + failed + ' / Total: ' + total;
}

function stopBulk() { bulkStopped = true; }

function addLogRow(phone, status) {
    const log = document.getElementById('bulkLog');
    const row = document.createElement('div');
    row.className = 'log-row';
    row.id = 'log-' + phone.replace(/[^a-zA-Z0-9]/g, '');
    row.innerHTML = '<span>' + escHtml(phone) + '</span><span class="log-wait">' + status + '</span>';
    log.insertBefore(row, log.firstChild);
}

function updateLogRow(phone, status) {
    const id = 'log-' + phone.replace(/[^a-zA-Z0-9]/g, '');
    const row = document.getElementById(id);
    if (row) {
        const cls = status.startsWith('✅') ? 'log-ok' : 'log-fail';
        row.querySelector('span:last-child').className = cls;
        row.querySelector('span:last-child').textContent = status;
    }
}

function escHtml(t) {
    const d = document.createElement('div');
    d.textContent = t;
    return d.innerHTML;
}

// ===== HEALTH CHECK =====
async function checkHealth() {
    const dot = document.getElementById('serverDot');
    const txt = document.getElementById('serverStatus');
    try {
        const r = await fetch('health.php');
        const d = await r.json();
        if (d.ok) {
            dot.className = 'status-dot online';
            txt.textContent = 'Server Online';
        } else {
            dot.className = 'status-dot offline';
            txt.textContent = 'Server Error';
        }
    } catch(e) {
        dot.className = 'status-dot offline';
        txt.textContent = 'Cannot reach server';
    }
}
checkHealth();
setInterval(checkHealth, 30000);
</script>

</body>
</html>
