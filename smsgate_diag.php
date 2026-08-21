<?php
/**
 * SmSGate Connectivity Diagnostic
 * Run this on your server to find why sms-gate.app is unreachable.
 * DELETE this file after diagnosis.
 */
header('Content-Type: text/html; charset=utf-8');

function test(string $label, callable $fn): array {
    $start = microtime(true);
    try {
        $result = $fn();
    } catch (Throwable $e) {
        $result = ['ok' => false, 'detail' => $e->getMessage()];
    }
    $result['ms'] = round((microtime(true) - $start) * 1000);
    $result['label'] = $label;
    return $result;
}

$tests = [];

// 1. DNS resolve
$tests[] = test('DNS — resolve api.sms-gate.app', function () {
    $ip = @gethostbyname('api.sms-gate.app');
    if ($ip === 'api.sms-gate.app') return ['ok' => false, 'detail' => 'DNS failed — could not resolve hostname'];
    return ['ok' => true, 'detail' => 'Resolved to ' . $ip];
});

// 2. TCP connect port 443
$tests[] = test('TCP — connect to api.sms-gate.app:443', function () {
    $sock = @fsockopen('api.sms-gate.app', 443, $errno, $errstr, 5);
    if (!$sock) return ['ok' => false, 'detail' => "Connection refused/blocked — $errstr ($errno)"];
    fclose($sock);
    return ['ok' => true, 'detail' => 'Port 443 reachable'];
});

// 3. cURL HTTPS HEAD request
$tests[] = test('HTTPS — cURL HEAD request', function () {
    $ch = curl_init('https://api.sms-gate.app');
    curl_setopt_array($ch, [
        CURLOPT_NOBODY         => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT        => 8,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_USERAGENT      => 'SpeedRadius-Diag/1.0',
    ]);
    $r         = curl_exec($ch);
    $http      = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr   = curl_error($ch);
    curl_close($ch);
    if ($curlErr)      return ['ok' => false, 'detail' => 'cURL error: ' . $curlErr];
    if ($http === 0)   return ['ok' => false, 'detail' => 'No response (HTTP 0)'];
    return ['ok' => true, 'detail' => 'HTTP ' . $http . ' — server is reachable'];
});

// 4. cURL with SSL verify OFF (checks if cert is the issue)
$tests[] = test('HTTPS — cURL without SSL verification', function () {
    $ch = curl_init('https://api.sms-gate.app');
    curl_setopt_array($ch, [
        CURLOPT_NOBODY         => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT        => 8,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);
    $r       = curl_exec($ch);
    $http    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);
    curl_close($ch);
    if ($curlErr) return ['ok' => false, 'detail' => 'cURL error: ' . $curlErr];
    return ['ok' => true, 'detail' => 'HTTP ' . $http . ' — reachable when SSL verify is OFF'];
});

// 5. Google connectivity sanity check
$tests[] = test('Sanity — can server reach google.com?', function () {
    $ch = curl_init('https://www.google.com');
    curl_setopt_array($ch, [
        CURLOPT_NOBODY         => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT        => 8,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $r       = curl_exec($ch);
    $http    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);
    curl_close($ch);
    if ($curlErr) return ['ok' => false, 'detail' => 'cURL error: ' . $curlErr];
    return ['ok' => true, 'detail' => 'HTTP ' . $http . ' — outbound internet works'];
});

// 6. PHP / cURL environment info
$env = [
    'PHP version'         => phpversion(),
    'cURL version'        => curl_version()['version'] ?? 'n/a',
    'OpenSSL version'     => curl_version()['ssl_version'] ?? 'n/a',
    'allow_url_fopen'     => ini_get('allow_url_fopen') ? 'On' : 'Off',
    'Server IP'           => $_SERVER['SERVER_ADDR'] ?? gethostbyname(gethostname()),
    'OS'                  => PHP_OS,
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>SmSGate Connectivity Diagnostic</title>
<style>
  body{font-family:'Segoe UI',sans-serif;background:#f0f4f8;color:#2d3436;margin:0;padding:24px}
  h1{color:#0f3460;margin-bottom:6px}
  p.sub{color:#636e72;margin-bottom:24px}
  .card{background:#fff;border-radius:10px;padding:22px 26px;box-shadow:0 2px 10px rgba(0,0,0,.07);margin-bottom:20px}
  h2{font-size:1em;text-transform:uppercase;letter-spacing:.5px;color:#555;margin-bottom:14px}
  .row{display:flex;align-items:flex-start;gap:14px;padding:11px 0;border-bottom:1px solid #f0f4f8}
  .row:last-child{border:none}
  .icon{font-size:1.3em;flex-shrink:0;width:28px;text-align:center}
  .label{font-weight:600;font-size:.93em;flex:1}
  .detail{font-size:.85em;color:#636e72;margin-top:2px}
  .ms{font-size:.78em;color:#b2bec3;margin-top:2px}
  .ok   .icon::before{content:'✅'}
  .fail .icon::before{content:'❌'}
  table{width:100%;border-collapse:collapse;font-size:.88em}
  td{padding:7px 10px;border-bottom:1px solid #f0f4f8}
  td:first-child{font-weight:600;color:#555;width:200px}
  .warn{background:#fff8e1;border:1px solid #ffe082;border-radius:8px;padding:12px 16px;font-size:.88em;color:#7b5e00;margin-top:16px}
</style>
</head>
<body>
<h1>📡 SmSGate Connectivity Diagnostic</h1>
<p class="sub">Testing whether your server can reach <strong>api.sms-gate.app</strong></p>

<div class="card">
  <h2>Test Results</h2>
  <?php foreach ($tests as $t): ?>
  <div class="row <?= $t['ok'] ? 'ok' : 'fail' ?>">
    <div class="icon"></div>
    <div>
      <div class="label"><?= htmlspecialchars($t['label']) ?></div>
      <div class="detail"><?= htmlspecialchars($t['detail']) ?></div>
      <div class="ms"><?= $t['ms'] ?>ms</div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<div class="card">
  <h2>Environment</h2>
  <table>
    <?php foreach ($env as $k => $v): ?>
    <tr><td><?= htmlspecialchars($k) ?></td><td><?= htmlspecialchars((string)$v) ?></td></tr>
    <?php endforeach; ?>
  </table>
</div>

<?php
$dnsOk    = $tests[0]['ok'];
$tcpOk    = $tests[1]['ok'];
$curlOk   = $tests[2]['ok'];
$noSslOk  = $tests[3]['ok'];
$googleOk = $tests[4]['ok'];
?>
<div class="card">
  <h2>Diagnosis &amp; Next Steps</h2>
  <?php if (!$googleOk && !$tcpOk): ?>
    <p>❌ <strong>Server has NO outbound internet access at all.</strong> Contact your hosting provider to allow outbound HTTPS (port 443) from this server.</p>
  <?php elseif (!$dnsOk): ?>
    <p>❌ <strong>DNS failure.</strong> The server cannot resolve <code>api.sms-gate.app</code>. Try adding a public DNS server (e.g. 8.8.8.8) to <code>/etc/resolv.conf</code> on your server.</p>
  <?php elseif (!$tcpOk): ?>
    <p>❌ <strong>Port 443 blocked.</strong> DNS works but TCP connection to port 443 is refused. A firewall rule on this server or your VPS provider is blocking outbound HTTPS. Add an iptables/firewall rule to allow outbound TCP port 443.</p>
  <?php elseif (!$curlOk && $noSslOk): ?>
    <p>⚠️ <strong>SSL certificate issue.</strong> The connection works but SSL verification fails. Run: <code>apt-get install ca-certificates</code> on the server, or temporarily set <code>CURLOPT_SSL_VERIFYPEER => false</code> in SMSGateGateway.php.</p>
  <?php elseif ($curlOk): ?>
    <p>✅ <strong>Connection is fine!</strong> The server CAN reach api.sms-gate.app. The timeout errors you saw earlier were likely a temporary outage or the phone was offline. Try sending a test SMS again.</p>
  <?php else: ?>
    <p>⚠️ Partial connectivity. Review the failed tests above and share the results for further help.</p>
  <?php endif; ?>

  <div class="warn">⚠️ <strong>Delete this file after use:</strong> <code>smsgate_diag.php</code> — do not leave diagnostic pages publicly accessible.</div>
</div>
</body>
</html>
