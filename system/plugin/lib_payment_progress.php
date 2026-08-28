<?php
/**
 * Shared "Payment Progress" page renderer.
 * Replaces the blocking alert() after an STK push with a live page that
 * polls the server every 3s and auto-detects when payment is confirmed.
 *
 * Usage:
 *   require_once 'lib_payment_progress.php';
 *   payment_progress_render($trxId);   // $trxId = tbl_payment_gateway.id
 */
function payment_progress_render($trxId, $timeoutSeconds = 35)
{
    $trxId = intval($trxId);
    $statusUrl = U . 'plugin/payment_status/' . $trxId;
    $viewUrl = U . 'order/view/' . $trxId;
    $timeoutSeconds = max(10, intval($timeoutSeconds));

    echo "<!DOCTYPE html>
<html lang='en'>
<head>
<meta charset='UTF-8'>
<meta name='viewport' content='width=device-width, initial-scale=1.0'>
<title>Processing Payment...</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI',Arial,sans-serif; }
  body { background:linear-gradient(135deg,#f8fafc,#eef2ff); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:16px; }
  .card { background:#fff; border-radius:20px; box-shadow:0 20px 50px rgba(15,23,42,.12); max-width:420px; width:100%; padding:36px 30px; text-align:center; border:1px solid #e2e8f0; }
  .ring { width:110px; height:110px; margin:0 auto 22px; position:relative; }
  .ring svg { transform:rotate(-90deg); }
  .ring .bg { stroke:#e2e8f0; }
  .ring .fg { stroke:#16a34a; stroke-linecap:round; transition:stroke-dashoffset .4s; }
  .ring .pct { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; font-size:22px; font-weight:800; color:#16a34a; }
  h1 { font-size:19px; color:#0f172a; margin-bottom:8px; }
  .sub { color:#64748b; font-size:13.5px; margin-bottom:20px; line-height:1.5; }
  .status { font-size:13.5px; font-weight:600; color:#334155; margin-bottom:8px; min-height:20px; }
  .timer { font-size:12px; color:#94a3b8; }
  .dots { display:inline-block; }
  .ok { color:#16a34a; font-size:15px; font-weight:700; }
  .err { color:#dc2626; font-size:15px; font-weight:700; }
</style>
</head>
<body>
  <div class='card'>
    <div class='ring'>
      <svg width='110' height='110' viewBox='0 0 110 110'>
        <circle class='bg' cx='55' cy='55' r='46' fill='none' stroke-width='9'></circle>
        <circle class='fg' id='ringFg' cx='55' cy='55' r='46' fill='none' stroke-width='9' stroke-dasharray='289' stroke-dashoffset='0'></circle>
      </svg>
      <div class='pct' id='pct'>0s</div>
    </div>
    <h1>Waiting for M-Pesa confirmation</h1>
    <p class='sub'>A payment request has been sent to your phone.<br>Enter your M-Pesa PIN to complete the payment.</p>
    <div class='status' id='status'>Checking payment status<span class='dots'>...</span></div>
    <div class='timer' id='timer'>Time remaining: {$timeoutSeconds}s</div>
  </div>

<script>
(function(){
  var total = {$timeoutSeconds};
  var elapsed = 0;
  var CIRC = 289;
  var done = false;

  function setPct(){
    var pct = Math.min(elapsed / total, 1);
    document.getElementById('ringFg').style.strokeDashoffset = CIRC * (1 - pct);
    var secs = Math.max(total - elapsed, 0);
    document.getElementById('pct').textContent = secs + 's';
    document.getElementById('timer').textContent = 'Time remaining: ' + secs + 's';
  }

  function showSuccess(msg){
    if(done) return; done = true;
    document.getElementById('status').innerHTML = '<span class=\"ok\">&#10003; ' + (msg||'Payment received!') + '</span>';
    setTimeout(function(){ window.location.href = '{$viewUrl}'; }, 1200);
  }
  function showError(msg){
    if(done) return; done = true;
    document.getElementById('status').innerHTML = '<span class=\"err\">' + (msg||'Payment not completed') + '</span>';
    setTimeout(function(){ window.location.href = '{$viewUrl}'; }, 1500);
  }

  function checkStatus(){
    fetch('{$statusUrl}')
      .then(function(r){ return r.json(); })
      .then(function(d){
        if(d.paid){ showSuccess(); return; }
        if(d.status === 3 || d.status === 4){ showError('Payment ' + (d.status===3?'failed':'cancelled')); return; }
        var dots = document.querySelector('.dots');
        if(dots){ dots.textContent = dots.textContent.length >= 4 ? '' : dots.textContent + '.'; }
      })
      .catch(function(){});
  }

  var tick = setInterval(function(){
    elapsed++;
    setPct();
    if(elapsed >= total){
      clearInterval(tick);
      showError('Payment not completed yet');
    }
  }, 1000);

  checkStatus();
  var poll = setInterval(function(){
    if(done){ clearInterval(poll); return; }
    checkStatus();
  }, 3000);
})();
</script>
</body>
</html>";
}
