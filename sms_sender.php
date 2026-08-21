<?php
/**
 * SmSGate Bulk / Single SMS Sender
 * Uses sms-gate.app Cloud API
 * Place in the root of SpeedRadius, open in browser.
 */

// ─── Load SpeedRadius config to read saved credentials ───────────────────────
$config_path = __DIR__ . '/config.php';
$db_creds    = [];
if (file_exists($config_path)) {
    // Parse only DB connection constants — avoid full bootstrap
    $raw = file_get_contents($config_path);
    preg_match("/define\('DB_HOST',\s*'([^']+)'\)/",  $raw, $m); $db_creds['host'] = $m[1] ?? 'localhost';
    preg_match("/define\('DB_USER',\s*'([^']+)'\)/",  $raw, $m); $db_creds['user'] = $m[1] ?? '';
    preg_match("/define\('DB_PASS',\s*'([^']+)'\)/",  $raw, $m); $db_creds['pass'] = $m[1] ?? '';
    preg_match("/define\('DB_NAME',\s*'([^']+)'\)/",  $raw, $m); $db_creds['name'] = $m[1] ?? '';
}

// ─── Load saved credentials from DB ──────────────────────────────────────────
$saved = [
    'username'  => 'sms',
    'password'  => 'Aujd1W5T',
    'device_id' => '00000000151dea350000019d4d3ec8df',
    'local_url' => 'http://129.222.187.131:8080',
];
if (!empty($db_creds['user'])) {
    try {
        $pdo = new PDO(
            "mysql:host={$db_creds['host']};dbname={$db_creds['name']};charset=utf8",
            $db_creds['user'],
            $db_creds['pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $rows = $pdo->query("SELECT setting, value FROM tbl_appconfig WHERE setting IN ('smsgate_username','smsgate_password','smsgate_device_id','smsgate_local_url')")->fetchAll(PDO::FETCH_KEY_PAIR);
        $saved['username']  = $rows['smsgate_username']  ?: $saved['username'];
        $saved['password']  = $rows['smsgate_password']  ?: $saved['password'];
        $saved['device_id'] = $rows['smsgate_device_id'] ?: $saved['device_id'];
        $saved['local_url'] = $rows['smsgate_local_url'] ?: $saved['local_url'];
    } catch (Exception $e) {
        // DB not available — using hardcoded defaults
    }
}

// ─── Phone formatter ──────────────────────────────────────────────────────────
function formatPhone(string $phone): string {
    $digits = preg_replace('/\D/', '', $phone);
    if (strlen($digits) === 9)                        return '+254' . $digits;   // 7xxxxxxxx
    if (strlen($digits) === 10 && $digits[0] === '0') return '+254' . substr($digits, 1);
    if (strlen($digits) === 12 && substr($digits, 0, 3) === '254') return '+' . $digits;
    if (strlen($digits) === 13 && $digits[0] === '+') return $digits; // already +254...
    return '+' . ltrim($digits, '+');
}

// ─── Send via sms-gate.app API ────────────────────────────────────────────────
function sendSMS(string $username, string $password, string $device_id, array $phones, string $message, string $local_url = ''): array {
    $formatted = array_map('formatPhone', $phones);
    $body = ['message' => $message, 'phoneNumbers' => $formatted];
    if (!empty($device_id)) $body['deviceId'] = $device_id;

    // Use local/ngrok URL if set, otherwise cloud
    if (!empty($local_url)) {
        $apiUrl  = rtrim($local_url, '/') . '/3rdparty/v1/message';
        $verifySsl = false;
    } else {
        $apiUrl  = 'https://api.sms-gate.app/3rdparty/v1/message';
        $verifySsl = true;
    }

    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($body),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_SSL_VERIFYPEER => $verifySsl,
        CURLOPT_SSL_VERIFYHOST => $verifySsl ? 2 : false,
        CURLOPT_USERPWD        => $username . ':' . $password,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Accept: application/json'],
    ]);
    $response  = curl_exec($ch);
    $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError)           return ['success' => false, 'error' => 'cURL: ' . $curlError];
    $result = json_decode($response, true);
    if ($httpCode === 202 || $httpCode === 200) {
        return ['success' => true, 'id' => $result['id'] ?? null, 'phones' => $formatted];
    }
    $err = $result['message'] ?? ('HTTP ' . $httpCode . ' — ' . $response);
    return ['success' => false, 'error' => $err];
}

// ─── Handle POST submission ───────────────────────────────────────────────────
$result  = null;
$results = [];   // bulk per-batch result

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = trim($_POST['username']  ?? '');
    $password  = trim($_POST['password']  ?? '');
    $device_id = trim($_POST['device_id'] ?? '');
    $local_url = trim($_POST['local_url'] ?? '');
    $message   = trim($_POST['message']   ?? '');
    $mode      = $_POST['mode']           ?? 'single';

    if ($mode === 'single') {
        $phone = trim($_POST['phone'] ?? '');
        if ($phone && $message && $username && $password) {
            $result = sendSMS($username, $password, $device_id, [$phone], $message, $local_url);
        } else {
            $result = ['success' => false, 'error' => 'All fields are required.'];
        }
    } else {
        // Bulk — split by newline / comma / semicolon
        $raw    = $_POST['phones'] ?? '';
        $phones = array_filter(array_map('trim', preg_split('/[\n\r,;]+/', $raw)));
        if (empty($phones)) {
            $result = ['success' => false, 'error' => 'Enter at least one phone number.'];
        } elseif (!$message || !$username || !$password) {
            $result = ['success' => false, 'error' => 'Credentials and message are required.'];
        } else {
            // Send in batches of 50
            $batches = array_chunk($phones, 50);
            $sent = 0; $failed = 0;
            foreach ($batches as $batch) {
                $r = sendSMS($username, $password, $device_id, $batch, $message, $local_url);
                if ($r['success']) $sent   += count($batch);
                else               $failed += count($batch);
                $results[] = $r;
            }
            $result = ['success' => $failed === 0, 'sent' => $sent, 'failed' => $failed, 'batches' => count($batches)];
        }
    }

    // Return JSON for AJAX calls
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['result' => $result, 'details' => $results]);
        exit;
    }
}

// ─── HTML ─────────────────────────────────────────────────────────────────────
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SmSGate SMS Sender — SpeedRadius</title>
<style>
  :root {
    --blue:#0f3460;--teal:#00b4d8;--green:#27ae60;--red:#e74c3c;
    --yellow:#f39c12;--bg:#f0f4f8;--card:#fff;--border:#dfe6e9;--text:#2d3436;
  }
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:'Segoe UI',sans-serif;background:var(--bg);color:var(--text);min-height:100vh}

  .header{background:linear-gradient(135deg,#1a1a2e,var(--blue));color:#fff;padding:24px 30px;display:flex;align-items:center;gap:14px}
  .header h1{font-size:1.5em;font-weight:700}
  .header p{opacity:.75;font-size:.9em;margin-top:3px}
  .header .icon{font-size:2em}

  .wrap{max-width:820px;margin:30px auto;padding:0 16px}

  /* Tabs */
  .tabs{display:flex;gap:6px;margin-bottom:20px}
  .tab{padding:9px 24px;border-radius:8px 8px 0 0;border:none;cursor:pointer;font-size:.92em;font-weight:600;background:#dde3ea;color:#555;transition:.2s}
  .tab.active{background:var(--card);color:var(--blue);border-bottom:3px solid var(--teal)}

  .card{background:var(--card);border-radius:0 12px 12px 12px;padding:28px 30px;box-shadow:0 2px 12px rgba(0,0,0,.07);margin-bottom:20px}

  label{display:block;font-weight:600;font-size:.85em;text-transform:uppercase;letter-spacing:.4px;color:#555;margin-bottom:5px;margin-top:14px}
  label:first-child{margin-top:0}
  input,textarea,select{width:100%;padding:10px 13px;border:1px solid var(--border);border-radius:7px;font-size:.95em;font-family:inherit;transition:border .2s;resize:vertical}
  input:focus,textarea:focus{outline:none;border-color:var(--teal);box-shadow:0 0 0 3px rgba(0,180,216,.12)}
  .row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
  .row3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px}

  .pass-wrap{position:relative}
  .pass-wrap input{padding-right:44px}
  .eye{position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:1.1em;color:#888}

  .btn{display:inline-flex;align-items:center;gap:8px;padding:11px 28px;border:none;border-radius:8px;font-size:.95em;font-weight:600;cursor:pointer;transition:.2s}
  .btn-primary{background:var(--teal);color:#fff}
  .btn-primary:hover{background:#0098b5}
  .btn-primary:disabled{opacity:.5;cursor:not-allowed}
  .btn-block{width:100%;justify-content:center;margin-top:18px}

  .alert{padding:13px 16px;border-radius:8px;margin-top:16px;font-size:.93em;display:flex;align-items:flex-start;gap:10px;border-left:4px solid}
  .alert-success{background:#eafaf1;border-color:var(--green)}
  .alert-error{background:#fdf2f2;border-color:var(--red)}
  .alert-icon{font-size:1.2em;flex-shrink:0}

  .counter{font-size:.8em;color:#888;text-align:right;margin-top:4px}
  .hint{font-size:.78em;color:#888;margin-top:4px}

  .spinner{display:none;width:18px;height:18px;border:3px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:spin .7s linear infinite}
  @keyframes spin{to{transform:rotate(360deg)}}

  .creds-note{background:#fff8e1;border:1px solid #ffe082;border-radius:8px;padding:11px 14px;font-size:.85em;color:#7b5e00;margin-bottom:16px}
</style>
</head>
<body>

<div class="header">
  <div class="icon">📱</div>
  <div>
    <h1>SmSGate SMS Sender</h1>
    <p>Send single or bulk SMS via your Android phone using sms-gate.app</p>
  </div>
</div>

<div class="wrap">

  <?php if (!$saved['username']): ?>
  <div class="creds-note">
    ⚠️ Credentials not auto-loaded from database. Fill them in manually below, or configure them in
    <strong>Admin Panel → SmSGate Gateway → Configuration</strong>.
  </div>
  <?php endif; ?>

  <!-- Tabs -->
  <div class="tabs">
    <button class="tab active" onclick="switchTab('single',this)">✉️ Single SMS</button>
    <button class="tab"        onclick="switchTab('bulk',this)">📋 Bulk SMS</button>
  </div>

  <div class="card">
    <form id="smsForm" onsubmit="sendForm(event)">
      <input type="hidden" name="mode" id="modeField" value="single">

      <!-- Credentials -->
      <div class="row3">
        <div>
          <label>Username</label>
          <input type="text" name="username" id="username"
                 value="<?= htmlspecialchars($saved['username']) ?>"
                 placeholder="From app home screen" required>
        </div>
        <div>
          <label>Password</label>
          <div class="pass-wrap">
            <input type="password" name="password" id="password"
                   value="<?= htmlspecialchars($saved['password']) ?>"
                   placeholder="From app home screen" required>
            <button type="button" class="eye" onclick="togglePass()">👁</button>
          </div>
        </div>
        <div>
          <label>Device ID <span style="font-weight:400;text-transform:none">(optional)</span></label>
          <input type="text" name="device_id" id="device_id"
                 value="<?= htmlspecialchars($saved['device_id']) ?>"
                 placeholder="Leave blank for auto">
        </div>
      </div>
      <div style="margin-top:14px">
        <label>Local / ngrok URL <span style="font-weight:400;text-transform:none">(optional — bypasses cloud)</span></label>
        <input type="text" name="local_url" id="local_url"
               value="<?= htmlspecialchars($saved['local_url']) ?>"
               placeholder="https://abc123.ngrok-free.app  or  http://192.168.1.x:8080">
        <div class="hint">Leave blank to use cloud (api.sms-gate.app). Fill this if your server can't reach the cloud.</div>
      </div>

      <hr style="margin:20px 0;border:none;border-top:1px solid var(--border)">

      <!-- SINGLE mode -->
      <div id="pane-single">
        <label>Phone Number</label>
        <input type="text" name="phone" id="phone" placeholder="07xx, 254xx, +254xx" autocomplete="off">
        <div class="hint">Formats accepted: 07xxxxxxxx, 254xxxxxxxxx, +254xxxxxxxxx</div>
      </div>

      <!-- BULK mode -->
      <div id="pane-bulk" style="display:none">
        <label>Phone Numbers</label>
        <textarea name="phones" id="phones" rows="6"
                  placeholder="One per line — or separate by comma / semicolon&#10;07112233445&#10;07211223344&#10;+254733445566"
                  oninput="countPhones()"></textarea>
        <div class="counter" id="phoneCount">0 numbers entered</div>
      </div>

      <!-- Message -->
      <label>Message</label>
      <textarea name="message" id="message" rows="4"
                placeholder="Type your message here…"
                oninput="countChars()" required></textarea>
      <div class="counter" id="charCount">0 / 160 characters</div>

      <button type="submit" class="btn btn-primary btn-block" id="sendBtn">
        <span id="btnLabel">Send SMS</span>
        <span class="spinner" id="spinner"></span>
      </button>
    </form>

    <!-- Result -->
    <div id="resultBox"></div>
  </div>

</div>

<script>
var currentTab = 'single';

function switchTab(tab, el) {
  currentTab = tab;
  document.getElementById('modeField').value = tab;
  document.getElementById('pane-single').style.display = tab === 'single' ? 'block' : 'none';
  document.getElementById('pane-bulk').style.display   = tab === 'bulk'   ? 'block' : 'none';
  document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
  el.classList.add('active');
  document.getElementById('btnLabel').textContent = tab === 'bulk' ? 'Send to All' : 'Send SMS';
  document.getElementById('resultBox').innerHTML = '';
}

function togglePass() {
  var p = document.getElementById('password');
  p.type = p.type === 'password' ? 'text' : 'password';
}

function countChars() {
  var len = document.getElementById('message').value.length;
  var parts = Math.ceil(len / 160) || 1;
  document.getElementById('charCount').textContent =
    len + ' characters' + (parts > 1 ? ' (' + parts + ' SMS parts)' : ' / 160');
}

function countPhones() {
  var raw = document.getElementById('phones').value.trim();
  var nums = raw ? raw.split(/[\n\r,;]+/).filter(s => s.trim() !== '') : [];
  document.getElementById('phoneCount').textContent = nums.length + ' number' + (nums.length !== 1 ? 's' : '') + ' entered';
}

function sendForm(e) {
  e.preventDefault();
  var btn     = document.getElementById('sendBtn');
  var spinner = document.getElementById('spinner');
  var label   = document.getElementById('btnLabel');
  btn.disabled = true;
  spinner.style.display = 'block';
  label.textContent = 'Sending…';

  var formData = new FormData(document.getElementById('smsForm'));

  fetch(window.location.href, {
    method: 'POST',
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
    body: formData
  })
  .then(r => r.json())
  .then(data => {
    showResult(data.result, data.details || []);
  })
  .catch(err => {
    showResult({ success: false, error: 'Network error: ' + err.message }, []);
  })
  .finally(() => {
    btn.disabled = false;
    spinner.style.display = 'none';
    label.textContent = currentTab === 'bulk' ? 'Send to All' : 'Send SMS';
  });
}

function showResult(r, details) {
  var box = document.getElementById('resultBox');
  if (r.success) {
    var msg = currentTab === 'bulk'
      ? '✅ Sent to <strong>' + r.sent + '</strong> number(s) in <strong>' + r.batches + '</strong> batch(es).'
      : '✅ SMS sent successfully!' + (r.id ? ' <small style="color:#888">ID: ' + r.id + '</small>' : '');
    box.innerHTML = '<div class="alert alert-success"><span class="alert-icon">✅</span><div>' + msg + '</div></div>';
  } else {
    var errMsg = '❌ ' + (r.error || 'Unknown error');
    if (r.sent !== undefined && r.failed > 0) {
      errMsg = '⚠️ Sent: ' + r.sent + ' | Failed: ' + r.failed;
    }
    box.innerHTML = '<div class="alert alert-error"><span class="alert-icon">❌</span><div>' + errMsg + '</div></div>';
  }
}
</script>
</body>
</html>
