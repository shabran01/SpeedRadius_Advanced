<?php
 
/**
 * PHP Mikrotik Billing (https://github.com/shabran01/SpeedRadius_Advanced/)
 *
 * This script is for updating SpeedRadius
 **/
session_start();
include "config.php";

if($db_password != null && ($db_pass == null || empty($db_pass))){
    // compability for old version
    $db_pass = $db_password;
}

if (empty($update_url)) {
    $update_url = 'https://github.com/shabran01/SpeedRadius_Advanced/archive/refs/heads/main.zip';
}


if(isset($_REQUEST['update_url']) && !empty($_REQUEST['update_url'])){
    $update_url = $_REQUEST['update_url'];
    $_SESSION['update_url'] = $update_url;
}

if(isset($_SESSION['update_url']) && !empty($_SESSION['update_url']) && $_SESSION['update_url'] != $update_url){
    $update_url = $_SESSION['update_url'];
}

if (!isset($_SESSION['aid']) || empty($_SESSION['aid'])) {
    r2("./?_route=login&You_are_not_admin", 'e', 'You are not admin');
}

set_time_limit(-1);

if (!is_writeable(pathFixer('system/cache/'))) {
    r2("./?_route=community", 'e', 'Folder system/cache/ is not writable');
}
if (!is_writeable(pathFixer('.'))) {
    r2("./?_route=community", 'e', 'Folder web is not writable');
}

$step = $_GET['step'];
$continue = true;
if (!extension_loaded('zip')) {
    $msg = "No PHP ZIP extension is available";
    $msgType = "danger";
    $continue = false;
}


$file = pathFixer('system/cache/SpeedRadius_Advanced.zip');
$folder = pathFixer('system/cache/SpeedRadius_Advanced-' . basename($update_url, ".zip") . '/');

if (empty($step)) {
    $step++;
} else if ($step == 1) {
    if (file_exists($file)) unlink($file);

    // Download update
    $fp = fopen($file, 'w+');
    $ch = curl_init($update_url);
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 600);
    curl_setopt($ch, CURLOPT_TIMEOUT, 600);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
    if (file_exists($file)) {
        $step++;
    } else {
        $msg = "Failed to download Update file";
        $msgType = "danger";
        $continue = false;
    }
} else if ($step == 2) {
    $zip = new ZipArchive();
    $zip->open($file);
    $zip->extractTo(pathFixer('system/cache/'));
    $zip->close();
    if (file_exists($folder)) {
        $step++;
    } else {
        $msg = "Failed to extract update file";
        $msgType = "danger";
        $continue = false;
    }
    // remove downloaded zip
    if (file_exists($file)) unlink($file);
} else if ($step == 3) {
    deleteFolder('system/autoload/');
    deleteFolder('system/vendor/');
    deleteFolder('ui/ui/');
    copyFolder($folder, pathFixer('./'));
    deleteFolder('install/');
    deleteFolder($folder);
    if (!file_exists($folder . pathFixer('/system/'))) {
        $step++;
    } else {
        $msg = "Failed to install update file.";
        $msgType = "danger";
        $continue = false;
    }
} else if ($step == 4) {
    if (file_exists("system/updates.json")) {
        $db = new pdo(
            "mysql:host=$db_host;dbname=$db_name",
            $db_user,
            $db_pass,
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );

        $updates = json_decode(file_get_contents("system/updates.json"), true);
        $dones = [];
        if (file_exists("system/cache/updates.done.json")) {
            $dones = json_decode(file_get_contents("system/cache/updates.done.json"), true);
        }
        foreach ($updates as $version => $queries) {
            if (!in_array($version, $dones)) {
                foreach ($queries as $q) {
                    try {
                        $db->exec($q);
                    } catch (PDOException $e) {
                        //ignore, it exists already
                    }
                }
                $dones[] = $version;
            }
        }
        file_put_contents("system/cache/updates.done.json", json_encode($dones));
    }
    $step++;
} else {
    $path = 'ui/compiled/';
    $files = scandir($path);
    foreach ($files as $file) {
        if (is_file($path . $file)) {
            unlink($path . $file);
        }
    }
    $version = json_decode(file_get_contents('version.json'), true)['version'];
    $continue = false;
}

function pathFixer($path)
{
    return str_replace("/", DIRECTORY_SEPARATOR, $path);
}

function r2($to, $ntype = 'e', $msg = '')
{
    if ($msg == '') {
        header("location: $to");
        die();
    }
    $_SESSION['ntype'] = $ntype;
    $_SESSION['notify'] = $msg;
    header("location: $to");
    die();
}

function copyFolder($from, $to, $exclude = [])
{
    // Paths to exclude from updates
    $excludePaths = [
        'settings/notifications',
        'plugin/whatsappGateway'
    ];
    
    $files = scandir($from);
    foreach ($files as $file) {
        if (is_file($from . $file) && !in_array($file, $exclude)) {
            if (file_exists($to . $file)) unlink($to . $file);
            rename($from . $file, $to . $file);
        } else if (is_dir($from . $file) && !in_array($file, ['.', '..'])) {
            // Check if this directory should be excluded from update
            $relativePath = str_replace(pathFixer('./'), '', $to . $file);
            $skipDir = false;
            foreach ($excludePaths as $excludePath) {
                if (strpos($relativePath, $excludePath) !== false) {
                    $skipDir = true;
                    break;
                }
            }
            
            if ($skipDir) {
                // Skip this directory during update
                continue;
            }
            
            if (!file_exists($to . $file)) {
                mkdir($to . $file);
            }
            copyFolder($from . $file . DIRECTORY_SEPARATOR, $to . $file . DIRECTORY_SEPARATOR);
        }
    }
}
function deleteFolder($path)
{
    $files = scandir($path);
    foreach ($files as $file) {
        if (is_file($path . $file)) {
            unlink($path . $file);
        } else if (is_dir($path . $file) && !in_array($file, ['.', '..'])) {
            deleteFolder($path . $file . DIRECTORY_SEPARATOR);
            rmdir($path . $file);
        }
    }
    rmdir($path);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>SpeedRadius — System Updater</title>
<link rel="shortcut icon" href="ui/ui/images/logo.png" type="image/x-icon">
<?php if ($continue) { ?>
<meta http-equiv="refresh" content="3; ./update.php?step=<?= $step ?>">
<?php } ?>
<style>
  * { margin:0; padding:0; box-sizing:border-box; }

  :root {
    --bg0:#0b1120; --bg1:#111827; --bg2:#1f2937;
    --line:rgba(148,163,184,.18);
    --text:#f1f5f9; --muted:#94a3b8;
    --accent:#6366f1; --accent-2:#a855f7;
    --success:#22c55e; --danger:#f87171;
    --card:rgba(17,24,39,.72);
  }

  html,body{height:100%}
  body{
    font-family:"Segoe UI",system-ui,-apple-system,Roboto,"Helvetica Neue",Arial,sans-serif;
    color:var(--text);
    min-height:100vh;
    display:flex; align-items:center; justify-content:center;
    padding:24px;
    background:
      radial-gradient(900px 600px at 8% -5%, rgba(99,102,241,.28), transparent 55%),
      radial-gradient(800px 600px at 105% 105%, rgba(168,85,247,.22), transparent 55%),
      linear-gradient(160deg,#0b1120,#111827 55%,#0f172a);
    background-attachment:fixed;
    overflow:hidden;
  }

  .orb{position:fixed;border-radius:50%;filter:blur(60px);opacity:.5;pointer-events:none;animation:float 12s ease-in-out infinite;z-index:0;}
  .orb-1{width:320px;height:320px;background:#4f46e5;top:-80px;left:-80px;}
  .orb-2{width:260px;height:260px;background:#7c3aed;bottom:-60px;right:-60px;animation-delay:-4s;}
  .orb-3{width:180px;height:180px;background:#0ea5e9;top:55%;left:60%;animation-delay:-8s;}
  @keyframes float{0%,100%{transform:translate(0,0) scale(1)}50%{transform:translate(30px,-30px) scale(1.08)}}

  .card{
    position:relative;z-index:2;
    width:100%;max-width:680px;
    background:var(--card);
    border:1px solid var(--line);
    border-radius:24px;
    backdrop-filter:blur(18px);
    -webkit-backdrop-filter:blur(18px);
    box-shadow:0 30px 60px -20px rgba(0,0,0,.65);
    padding:40px 36px 32px;
    animation:pop .5s cubic-bezier(.2,.9,.3,1.2);
  }
  @keyframes pop{from{opacity:0;transform:translateY(24px) scale(.97)}to{opacity:1;transform:none}}

  .head{display:flex;align-items:center;gap:16px;margin-bottom:30px;}
  .logo{
    width:58px;height:58px;border-radius:16px;flex:none;
    display:flex;align-items:center;justify-content:center;
    background:linear-gradient(135deg,var(--accent),var(--accent-2));
    box-shadow:0 10px 24px -8px rgba(99,102,241,.7);
    position:relative;
  }
  .logo svg{width:30px;height:30px;color:#fff}
  .logo::after{content:"";position:absolute;inset:0;border-radius:16px;box-shadow:inset 0 0 0 1px rgba(255,255,255,.25)}
  .head h1{font-size:20px;font-weight:700;letter-spacing:.2px}
  .head p{color:var(--muted);font-size:13px;margin-top:2px}
  .version{margin-left:auto;font-size:12px;font-weight:600;color:var(--accent-2);background:rgba(168,85,247,.12);border:1px solid rgba(168,85,247,.3);padding:6px 12px;border-radius:999px;white-space:nowrap}

  /* stepper */
  .stepper{display:flex;align-items:flex-start;margin:0 0 32px;}
  .step{flex:1;display:flex;flex-direction:column;align-items:center;position:relative;text-align:center;}
  .step:not(:last-child)::after{
    content:"";position:absolute;top:17px;left:calc(50% + 20px);width:calc(100% - 40px);height:3px;
    background:var(--line);border-radius:2px;z-index:0;
  }
  .step.done:not(:last-child)::after{background:linear-gradient(90deg,var(--success),rgba(34,197,94,.35));}
  .dot{
    width:36px;height:36px;border-radius:50%;z-index:1;
    display:flex;align-items:center;justify-content:center;
    background:var(--bg2);border:2px solid var(--line);
    color:var(--muted);font-size:13px;font-weight:700;
    transition:.35s ease;
  }
  .step.done .dot{background:var(--success);border-color:var(--success);color:#06220f;}
  .step.active .dot{
    background:linear-gradient(135deg,var(--accent),var(--accent-2));
    border-color:transparent;color:#fff;
    animation:pulse 1.6s ease-in-out infinite;
  }
  @keyframes pulse{0%,100%{box-shadow:0 0 0 4px rgba(99,102,241,.25)}50%{box-shadow:0 0 0 9px rgba(99,102,241,.12)}}
  .step span{font-size:11px;margin-top:8px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.5px;}
  .step.done span{color:var(--success);}
  .step.active span{color:var(--text);}

  /* status body */
  .body{
    border:1px solid var(--line);border-radius:16px;
    padding:28px 22px;background:rgba(15,23,42,.5);
    min-height:170px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:14px;text-align:center;
  }
  .status-title{font-size:16px;font-weight:700;}
  .status-sub{color:var(--muted);font-size:13.5px;line-height:1.6;max-width:460px;}
  .status-sub b{color:var(--text);}

  .spinner{
    width:46px;height:46px;border-radius:50%;
    border:3px solid rgba(99,102,241,.2);border-top-color:var(--accent);
    animation:spin .8s linear infinite;
  }
  @keyframes spin{to{transform:rotate(360deg)}}

  .check{
    width:60px;height:60px;border-radius:50%;display:flex;align-items:center;justify-content:center;
    background:rgba(34,197,94,.15);border:2px solid var(--success);
    animation:popCheck .5s cubic-bezier(.2,.9,.3,1.4) both;
  }
  .check svg{width:32px;height:32px;color:var(--success)}
  @keyframes popCheck{from{transform:scale(.4);opacity:0}to{transform:scale(1);opacity:1}}

  .alert{
    width:100%;border-radius:12px;padding:14px 16px;font-size:13.5px;display:flex;gap:12px;align-items:flex-start;text-align:left;
  }
  .alert.danger{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.35);color:#fecaca;}
  .alert svg{width:20px;height:20px;flex:none;margin-top:1px}

  .btn{
    display:inline-flex;align-items:center;gap:8px;
    background:linear-gradient(135deg,var(--accent),var(--accent-2));
    color:#fff;font-weight:600;font-size:14px;border:none;border-radius:12px;
    padding:12px 22px;cursor:pointer;text-decoration:none;
    box-shadow:0 10px 24px -10px rgba(99,102,241,.8);
    transition:.25s ease;
  }
  .btn:hover{transform:translateY(-2px);box-shadow:0 14px 30px -10px rgba(99,102,241,.9)}
  .btn.secondary{background:var(--bg2);box-shadow:none;border:1px solid var(--line)}
  .btn.secondary:hover{background:#273449;transform:none}

  .foot{margin-top:26px;text-align:center;color:var(--muted);font-size:12px;}
  .foot a{color:var(--muted);text-decoration:none;font-weight:600;}
  .foot a:hover{color:#fff}

  @media (max-width:560px){
    body{padding:16px;}
    .card{padding:26px 18px 22px;border-radius:18px;}
    .head h1{font-size:17px;}
    .version{font-size:10.5px;padding:5px 9px;}
    .step span{font-size:9.5px;}
    .dot{width:30px;height:30px;font-size:12px;}
    .step:not(:last-child)::after{top:14px;left:calc(50% + 17px);width:calc(100% - 34px);}
  }
</style>
</head>
<body>
<div class="orb orb-1"></div>
<div class="orb orb-2"></div>
<div class="orb orb-3"></div>

<div class="card">
  <div class="head">
    <div class="logo">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 3 14h7l-1 8 10-12h-7l1-8z"/></svg>
    </div>
    <div>
      <h1>SpeedRadius Updater</h1>
      <p>System update &amp; maintenance</p>
    </div>
    <?php if (!empty($version)) { ?>
      <span class="version">v<?= htmlspecialchars($version) ?></span>
    <?php } ?>
  </div>

  <?php
    $labels = [1=>'Download',2=>'Extract',3=>'Install',4=>'Database',5=>'Done'];
    $subs   = [1=>'Downloading the latest update package from the repository…',
               2=>'Extracting the update archive…',
               3=>'Installing new files and clearing old dependencies…',
               4=>'Applying database migrations…'];
  ?>

  <div class="stepper">
    <?php for ($i = 1; $i <= 5; $i++) {
        $cls = '';
        if ($step == 5 || $i < $step) $cls = 'done';
        elseif ($i == $step) $cls = 'active';
    ?>
    <div class="step <?= $cls ?>">
      <div class="dot">
        <?php if ($step == 5 || $i < $step) { ?>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><path d="M20 6 9 17l-5-5"/></svg>
        <?php } else { echo $i; } ?>
      </div>
      <span><?= $labels[$i] ?></span>
    </div>
    <?php } ?>
  </div>

  <div class="body">
    <?php if (!empty($msgType) && !empty($msg)) { ?>
      <div class="alert <?= $msgType ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        <div><strong>Update failed</strong><br><?= htmlspecialchars($msg) ?></div>
      </div>
      <a class="btn secondary" href="./update.php?step=1">Retry update</a>
    <?php } elseif ($step == 5) { ?>
      <div class="check">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
      </div>
      <div class="status-title">Update complete</div>
      <div class="status-sub">SpeedRadius has been updated to version <b><?= htmlspecialchars($version) ?></b>. Redirecting to the dashboard…</div>
      <a class="btn" href="./?_route=dashboard">Go to dashboard</a>
      <meta http-equiv="refresh" content="5; ./?_route=dashboard">
    <?php } else { ?>
      <div class="spinner"></div>
      <div class="status-title"><?= $labels[$step] ?? 'Processing' ?></div>
      <div class="status-sub"><?= $subs[$step] ?? 'Please wait…' ?></div>
    <?php } ?>
  </div>

  <div class="foot">Powered by <a href="https://github.com/shabran01/SpeedRadius_Advanced" target="_blank" rel="noopener">SpeedRadius</a> &middot; Shabran Kweyu</div>
</div>
</body>
</html>
