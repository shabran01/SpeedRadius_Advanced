<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{ucwords(Lang::T($type))} - {$_c['CompanyName']}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="shortcut icon" href="{$UPLOAD_PATH}/logo.png" type="image/x-icon" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Native redirect -->
    <meta http-equiv="refresh" content="{$time}; url={$url}">
    
    <style>
{literal}
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-dark: #0f0c29;
            --text-white: #f8fafc;
            --text-muted: rgba(255,255,255,0.65);
        }

        html, body { height: 100%; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: var(--bg-dark);
            color: var(--text-white);
        }

        /* ─── ANIMATED BACKGROUND ─── */
        .bg-mesh {
            position: fixed;
            inset: 0;
            z-index: 0;
            background:
                radial-gradient(ellipse at 20% 50%, rgba(99,102,241,0.2) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(6,182,212,0.15) 0%, transparent 55%),
                radial-gradient(ellipse at 60% 80%, rgba(139,92,246,0.15) 0%, transparent 55%),
                linear-gradient(135deg, #0f0c29 0%, #1a1040 50%, #0d1b3e 100%);
            animation: meshShift 12s ease-in-out infinite alternate;
        }

        @keyframes meshShift {
            0%   { filter: hue-rotate(0deg); }
            100% { filter: hue-rotate(15deg); }
        }

        /* ─── FLOATING ORBS ─── */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.3;
            pointer-events: none;
            z-index: 0;
            animation: floatOrb linear infinite;
        }
        .orb-1 { width: 400px; height: 400px; background: #10b981; top: -100px; left: -100px; animation-duration: 20s; } /* Green accent */
        .orb-2 { width: 350px; height: 350px; background: #6366f1; bottom: -100px; right: -50px; animation-duration: 25s; animation-direction: reverse; }
        .orb-3 { width: 250px; height: 250px; background: #ef4444; top: 30%; left: 60%; animation-duration: 30s; opacity: 0.15; } /* Red accent */

        @keyframes floatOrb {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33%       { transform: translate(20px, -30px) scale(1.05); }
            66%       { transform: translate(-15px, 20px) scale(0.95); }
        }

        /* ─── ALERT CARD ─── */
        .alert-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 
                0 32px 64px rgba(0,0,0,0.4),
                0 0 0 1px rgba(255,255,255,0.05) inset;
            animation: slideUp 0.6s cubic-bezier(0.22, 1, 0.36, 1) both;
            margin: 0 20px;
            text-align: center;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px) scale(0.95); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ─── DYNAMIC HEADERS ─── */
        .header-block {
            padding: 40px 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            position: relative;
            overflow: hidden;
        }
        
        .header-bg {
            position: absolute;
            inset: 0;
            opacity: 0.15;
            z-index: 0;
        }

        .alert-success .header-bg { background: linear-gradient(135deg, #10b981, #059669); }
        .alert-danger .header-bg  { background: linear-gradient(135deg, #ef4444, #be123c); }
        .alert-info .header-bg    { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }

        .icon-ring {
            width: 80px; height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: #fff;
            position: relative;
            z-index: 1;
            margin-bottom: 20px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.2);
            animation: pulse-ring 2s infinite;
        }

        @keyframes pulse-ring {
            0% { box-shadow: 0 0 0 0 rgba(255,255,255,0.2); }
            70% { box-shadow: 0 0 0 20px rgba(255,255,255,0); }
            100% { box-shadow: 0 0 0 0 rgba(255,255,255,0); }
        }

        .alert-success .icon-ring { background: linear-gradient(135deg, #34d399, #059669); }
        .alert-danger .icon-ring  { background: linear-gradient(135deg, #f87171, #be123c); }
        .alert-info .icon-ring    { background: linear-gradient(135deg, #60a5fa, #2563eb); }

        .header-title {
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            position: relative;
            z-index: 1;
        }

        /* ─── BODY CONTENT ─── */
        .body-block {
            padding: 36px 40px;
            background: rgba(0,0,0,0.1);
        }

        .message-text {
            font-size: 1.05rem;
            line-height: 1.6;
            color: rgba(255,255,255,0.9);
            margin-bottom: 32px;
            font-weight: 500;
        }

        /* ─── BUTTON / PROGRESS ─── */
        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 14px;
            border-radius: 14px;
            text-decoration: none;
            color: #fff;
            font-family: 'Inter', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 0.3px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
        }

        .btn-action i { margin-right: 10px; font-size: 1.1rem; }

        .alert-success .btn-action { background: linear-gradient(135deg, #10b981, #059669); }
        .alert-danger .btn-action  { background: linear-gradient(135deg, #ef4444, #be123c); }
        .alert-info .btn-action    { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(0,0,0,0.3);
            filter: brightness(1.1);
        }

        .progress-track {
            width: 100%;
            height: 6px;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            margin-top: 24px;
            overflow: hidden;
            position: relative;
        }

        .progress-bar {
            height: 100%;
            width: 100%;
            border-radius: 10px;
            transition: width 1s linear;
        }

        .alert-success .progress-bar { background: #34d399; }
        .alert-danger .progress-bar  { background: #f87171; }
        .alert-info .progress-bar    { background: #60a5fa; }

        .auto-redirect-txt {
            display: block;
            margin-top: 14px;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* ─── FOOTER ─── */
        .company-footer {
            position: absolute;
            bottom: 30px;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 0.875rem;
            color: rgba(255,255,255,0.4);
            font-weight: 500;
            z-index: 10;
        }
{/literal}
    </style>
</head>

<body class="alert-{if $type == 'success'}success{elseif $type == 'danger'}danger{else}info{/if}">
    <!-- Background elements -->
    <div class="bg-mesh"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="alert-card">
        <!-- Header -->
        <div class="header-block">
            <div class="header-bg"></div>
            <div class="icon-ring">
                {if $type == 'success'}
                    <i class="fa fa-check"></i>
                {elseif $type == 'danger'}
                    <i class="fa fa-xmark"></i>
                {else}
                    <i class="fa fa-info"></i>
                {/if}
            </div>
            <h2 class="header-title">{ucwords(Lang::T($type))}</h2>
        </div>
        
        <!-- Body -->
        <div class="body-block">
            <p class="message-text">{$text}</p>
            
            <a href="{$url}" class="btn-action">
                <i class="fa fa-circle-notch fa-spin" id="spinner-icon"></i>
                <span id="btn-text">{Lang::T('Click Here')} ({$time})</span>
            </a>
            
            <div class="progress-track">
                <div class="progress-bar" id="progress-bar"></div>
            </div>
            
            <span class="auto-redirect-txt">Redirecting automatically...</span>
        </div>
    </div>

    <!-- Footer -->
    <div class="company-footer">
        &copy; {$_c['CompanyName']}
    </div>

    <script>
{literal}
        document.addEventListener('DOMContentLoaded', function() {
            var timeRemaining = parseInt('{/literal}{$time}{literal}', 10);
            var initialTime = timeRemaining;
            var btnText = document.getElementById('btn-text');
            var progressBar = document.getElementById('progress-bar');
            var spinner = document.getElementById('spinner-icon');
            
            function tick() {
                if (timeRemaining <= 0) {
                    btnText.innerHTML = "Redirecting now...";
                    progressBar.style.width = "0%";
                    spinner.classList.remove('fa-circle-notch', 'fa-spin');
                    spinner.classList.add('fa-arrow-right-to-bracket');
                    return;
                }
                
                timeRemaining--;
                
                // Update text
                btnText.innerHTML = "{/literal}{Lang::T('Click Here')}{literal} (" + timeRemaining + ")";
                
                // Update progress bar width
                var percent = (timeRemaining / initialTime) * 100;
                progressBar.style.width = percent + "%";
                
                setTimeout(tick, 1000);
            }
            
            // Start the tick after a very short delay to allow transition to register
            setTimeout(tick, 1000);
        });
{/literal}
    </script>
</body>
</html>