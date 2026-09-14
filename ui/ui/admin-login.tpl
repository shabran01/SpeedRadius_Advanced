<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{Lang::T('Login')} - {$_c['CompanyName']}</title>
    <meta name="description" content="Admin Login - {$_c['CompanyName']} Management Portal">
    <link rel="shortcut icon" href="ui/ui/images/logo.png" type="image/x-icon" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
{literal}
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --primary-light: #818cf8;
            --accent: #06b6d4;
            --accent2: #8b5cf6;
            --bg-dark: #0f0c29;
            --glass-bg: rgba(255,255,255,0.07);
            --glass-border: rgba(255,255,255,0.15);
            --text-white: #f8fafc;
            --text-muted: rgba(255,255,255,0.6);
        }

        html, body { height: 100%; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            overflow: hidden;
            background: #0f0c29;
        }

        /* ─── ANIMATED BACKGROUND ─── */
        .bg-mesh {
            position: fixed;
            inset: 0;
            z-index: 0;
            background:
                radial-gradient(ellipse at 20% 50%, rgba(99,102,241,0.35) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(6,182,212,0.3) 0%, transparent 55%),
                radial-gradient(ellipse at 60% 80%, rgba(139,92,246,0.3) 0%, transparent 55%),
                linear-gradient(135deg, #0f0c29 0%, #1a1040 40%, #0d1b3e 100%);
            animation: meshShift 12s ease-in-out infinite alternate;
        }

        @keyframes meshShift {
            0%   { filter: hue-rotate(0deg) brightness(1); }
            100% { filter: hue-rotate(25deg) brightness(1.08); }
        }

        /* ─── FLOATING ORBS ─── */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.4;
            pointer-events: none;
            z-index: 0;
            animation: floatOrb linear infinite;
        }
        .orb-1 { width: 500px; height: 500px; background: #6366f1; top: -200px; left: -150px; animation-duration: 18s; }
        .orb-2 { width: 400px; height: 400px; background: #06b6d4; bottom: -150px; right: -100px; animation-duration: 22s; animation-direction: reverse; }
        .orb-3 { width: 300px; height: 300px; background: #8b5cf6; top: 40%; left: 30%; animation-duration: 28s; opacity: 0.25; }

        @keyframes floatOrb {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33%       { transform: translate(30px, -40px) scale(1.05); }
            66%       { transform: translate(-20px, 30px) scale(0.97); }
        }

        /* ─── PARTICLES ─── */
        .particles { position: fixed; inset: 0; z-index: 0; overflow: hidden; pointer-events: none; }
        .particle {
            position: absolute;
            width: 3px; height: 3px;
            border-radius: 50%;
            background: rgba(255,255,255,0.6);
            animation: rise linear infinite;
        }
        @keyframes rise {
            0%   { transform: translateY(100vh) scale(0); opacity: 0; }
            10%  { opacity: 0.8; }
            90%  { opacity: 0.4; }
            100% { transform: translateY(-10vh) scale(1.5); opacity: 0; }
        }

        /* ─── LAYOUT ─── */
        .page-wrapper {
            position: relative;
            z-index: 1;
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* ─── LEFT PANEL (branding) ─── */
        .left-panel {
            display: none;
            flex: 1;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding: 60px 70px;
        }

        @media (min-width: 1024px) {
            .left-panel { display: flex; }
        }

        .brand-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(99,102,241,0.2);
            border: 1px solid rgba(99,102,241,0.4);
            border-radius: 100px;
            padding: 6px 16px;
            font-size: 12px;
            font-weight: 600;
            color: #a5b4fc;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 32px;
        }

        .brand-tag .dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: #6ee7b7;
            box-shadow: 0 0 8px #6ee7b7;
            animation: pulse-dot 2s ease-in-out infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { transform: scale(1); opacity: 1; }
            50%       { transform: scale(1.4); opacity: 0.7; }
        }

        .brand-headline {
            font-size: clamp(2.2rem, 4vw, 3.5rem);
            font-weight: 800;
            color: #fff;
            line-height: 1.15;
            margin-bottom: 20px;
            letter-spacing: -1px;
        }

        .brand-headline span {
            background: linear-gradient(90deg, #818cf8, #06b6d4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-desc {
            font-size: 1.05rem;
            color: var(--text-muted);
            line-height: 1.7;
            max-width: 440px;
            margin-bottom: 48px;
        }

        .feature-list { display: flex; flex-direction: column; gap: 16px; }
        .feature-item {
            display: flex;
            align-items: center;
            gap: 14px;
            color: rgba(255,255,255,0.8);
            font-size: 0.94rem;
            font-weight: 500;
        }

        .feature-icon {
            width: 36px; height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
        }

        .fi-1 { background: rgba(99,102,241,0.2); color: #818cf8; }
        .fi-2 { background: rgba(6,182,212,0.2);  color: #22d3ee; }
        .fi-3 { background: rgba(16,185,129,0.2); color: #34d399; }
        .fi-4 { background: rgba(245,158,11,0.2); color: #fbbf24; }

        /* ─── RIGHT PANEL (login card) ─── */
        .right-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            width: 100%;
        }

        @media (min-width: 1024px) {
            .right-panel {
                width: 480px;
                min-width: 480px;
                flex-shrink: 0;
                padding: 40px;
            }
        }

        /* ─── GLASS CARD ─── */
        .login-card {
            width: 100%;
            max-width: 420px;
            background: rgba(255,255,255,0.06);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 28px;
            padding: 44px 40px;
            box-shadow:
                0 32px 64px rgba(0,0,0,0.35),
                0 0 0 1px rgba(255,255,255,0.05) inset,
                0 1px 0 rgba(255,255,255,0.15) inset;
            animation: cardIn 0.7s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(30px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ─── LOGO AREA ─── */
        .logo-ring {
            width: 72px; height: 72px;
            border-radius: 20px;
            background: linear-gradient(135deg, #6366f1, #06b6d4);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 28px;
            color: #fff;
            box-shadow: 0 12px 30px rgba(99,102,241,0.4);
            position: relative;
        }

        .logo-ring::before {
            content: '';
            position: absolute;
            inset: -3px;
            border-radius: 23px;
            background: linear-gradient(135deg, rgba(99,102,241,0.6), rgba(6,182,212,0.6));
            z-index: -1;
            filter: blur(8px);
            opacity: 0.7;
        }

        .card-title {
            text-align: center;
            font-size: 1.6rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 6px;
            letter-spacing: -0.5px;
        }

        .card-sub {
            text-align: center;
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-bottom: 32px;
        }

        /* ─── ALERT ─── */
        .alert-box {
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 22px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 0.875rem;
            font-weight: 500;
            animation: alertIn 0.4s ease;
        }
        @keyframes alertIn {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .alert-error {
            background: rgba(239,68,68,0.12);
            border: 1px solid rgba(239,68,68,0.3);
            color: #fca5a5;
        }
        .alert-success {
            background: rgba(16,185,129,0.12);
            border: 1px solid rgba(16,185,129,0.3);
            color: #6ee7b7;
        }

        /* ─── FORM FIELDS ─── */
        .field-group { margin-bottom: 18px; }

        .field-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: rgba(255,255,255,0.75);
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }

        .field-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .field-icon {
            position: absolute;
            left: 14px;
            color: rgba(255,255,255,0.35);
            font-size: 14px;
            pointer-events: none;
            transition: color 0.2s;
        }

        .field-input {
            width: 100%;
            padding: 13px 14px 13px 42px;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 12px;
            font-size: 0.938rem;
            font-family: 'Inter', sans-serif;
            color: #fff;
            outline: none;
            transition: all 0.25s ease;
            caret-color: #818cf8;
        }

        .field-input::placeholder { color: rgba(255,255,255,0.25); }

        .field-input:focus {
            background: rgba(255,255,255,0.1);
            border-color: rgba(99,102,241,0.7);
            box-shadow: 0 0 0 3px rgba(99,102,241,0.2), 0 0 20px rgba(99,102,241,0.1);
        }

        .field-input:focus + .field-icon,
        .field-wrap:focus-within .field-icon {
            color: #818cf8;
        }

        .toggle-pw {
            position: absolute;
            right: 14px;
            background: none;
            border: none;
            color: rgba(255,255,255,0.3);
            cursor: pointer;
            font-size: 14px;
            transition: color 0.2s;
            padding: 4px;
            display: flex;
            align-items: center;
        }

        .toggle-pw:hover { color: #818cf8; }

        /* ─── LOGIN BUTTON ─── */
        .btn-login {
            width: 100%;
            padding: 14px;
            margin-top: 8px;
            border: none;
            border-radius: 12px;
            font-size: 0.975rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            color: #fff;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            letter-spacing: 0.3px;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 50%, #06b6d4 100%);
            background-size: 200% 200%;
            background-position: 0% 50%;
            transition: background-position 0.4s ease, transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 8px 24px rgba(99,102,241,0.35);
        }

        .btn-login:hover {
            background-position: 100% 50%;
            transform: translateY(-2px);
            box-shadow: 0 14px 32px rgba(99,102,241,0.45);
        }

        .btn-login:active { transform: translateY(0); }

        .btn-login .btn-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
        }

        .btn-login.loading .btn-content { display: none; }
        .btn-login .spinner { display: none; }
        .btn-login.loading .spinner {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @keyframes spin { to { transform: rotate(360deg); } }
        .spin { animation: spin 0.8s linear infinite; }

        /* ─── RIPPLE ─── */
        .btn-login::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle, rgba(255,255,255,0.25) 0%, transparent 70%);
            opacity: 0;
            transform: scale(0.5);
            transition: opacity 0.4s, transform 0.5s;
        }

        .btn-login:active::after {
            opacity: 1;
            transform: scale(2);
            transition: opacity 0s, transform 0s;
        }

        /* ─── FORGOT PASSWORD ─── */
        .forgot-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: 0.875rem;
            color: rgba(255,255,255,0.45);
            text-decoration: none;
            transition: color 0.2s;
        }

        .forgot-link:hover { color: #818cf8; }

        /* ─── FOOTER ─── */
        .card-footer {
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.07);
            text-align: center;
            font-size: 0.78rem;
            color: rgba(255,255,255,0.25);
        }

        /* ─── THEME TOGGLE ─── */
        .theme-toggle-btn {
            position: fixed;
            top: 18px;
            right: 18px;
            z-index: 100;
            width: 42px; height: 42px;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.15);
            background: rgba(255,255,255,0.07);
            backdrop-filter: blur(12px);
            color: rgba(255,255,255,0.7);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            transition: all 0.25s;
        }

        .theme-toggle-btn:hover {
            background: rgba(99,102,241,0.3);
            color: #fff;
            border-color: rgba(99,102,241,0.5);
            transform: scale(1.08);
        }

        /* ─── LIGHT MODE OVERRIDES ─── */
        body.light-mode {
            background: #f1f5f9;
        }

        body.light-mode .bg-mesh {
            background:
                radial-gradient(ellipse at 20% 50%, rgba(99,102,241,0.12) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(6,182,212,0.1) 0%, transparent 55%),
                linear-gradient(135deg, #e0e7ff 0%, #f0f9ff 50%, #faf5ff 100%);
        }

        body.light-mode .orb { opacity: 0.12; }

        body.light-mode .particle { background: rgba(99,102,241,0.4); }

        body.light-mode .login-card {
            background: rgba(255,255,255,0.82);
            border-color: rgba(99,102,241,0.12);
            box-shadow: 0 24px 60px rgba(0,0,0,0.12);
        }

        body.light-mode .card-title { color: #1e1b4b; }
        body.light-mode .card-sub   { color: #64748b; }
        body.light-mode .field-label { color: #475569; }

        body.light-mode .field-input {
            background: rgba(241,245,249,0.8);
            border-color: #e2e8f0;
            color: #1e1b4b;
        }

        body.light-mode .field-input::placeholder { color: #94a3b8; }

        body.light-mode .field-input:focus {
            background: #fff;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
        }

        body.light-mode .field-icon { color: #94a3b8; }
        body.light-mode .toggle-pw { color: #94a3b8; }
        body.light-mode .forgot-link { color: #6366f1; }
        body.light-mode .card-footer { color: #94a3b8; border-color: #e2e8f0; }

        body.light-mode .left-panel .brand-headline { color: #1e1b4b; }
        body.light-mode .left-panel .brand-desc     { color: #64748b; }
        body.light-mode .left-panel .feature-item   { color: #334155; }
        body.light-mode .left-panel .brand-tag      { background: rgba(99,102,241,0.1); color: #4f46e5; border-color: rgba(99,102,241,0.2); }

        body.light-mode .alert-error  { background: rgba(239,68,68,0.08);  color: #dc2626; }
        body.light-mode .alert-success { background: rgba(16,185,129,0.08); color: #059669; }

        body.light-mode .theme-toggle-btn { background: rgba(99,102,241,0.1); color: #6366f1; border-color: rgba(99,102,241,0.2); }
{/literal}
    </style>
</head>

<body>
    <!-- Background elements -->
    <div class="bg-mesh"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="particles" id="particles"></div>

    <!-- Theme Toggle -->
    <button class="theme-toggle-btn" id="themeBtn" title="Toggle Light/Dark Mode">
        <i class="fa fa-moon" id="themeIcon"></i>
    </button>

    <div class="page-wrapper">
        <!-- ── LEFT PANEL ── -->
        <div class="left-panel">
            <div class="brand-tag">
                <span class="dot"></span>
                Network Management
            </div>
            <h1 class="brand-headline">
                Welcome to<br>
                <span>{$_c['CompanyName']}</span><br>
                Admin Portal
            </h1>
            <p class="brand-desc">
                Your all-in-one ISP management platform. Monitor users, manage billing, track performance, and control your entire network from one place.
            </p>
            <div class="feature-list">
                <div class="feature-item">
                    <div class="feature-icon fi-1"><i class="fa fa-wifi"></i></div>
                    Real-time Network Monitoring
                </div>
                <div class="feature-item">
                    <div class="feature-icon fi-2"><i class="fa fa-chart-line"></i></div>
                    Revenue & Sales Analytics
                </div>
                <div class="feature-item">
                    <div class="feature-icon fi-3"><i class="fa fa-users"></i></div>
                    Customer Management
                </div>
                <div class="feature-item">
                    <div class="feature-icon fi-4"><i class="fa fa-shield-halved"></i></div>
                    Secure & Encrypted Access
                </div>
            </div>
        </div>

        <!-- ── RIGHT PANEL (login card) ── -->
        <div class="right-panel">
            <div class="login-card">
                <!-- Logo -->
                <div class="logo-ring">
                    <i class="fa fa-lock"></i>
                </div>

                <h2 class="card-title">{$_c['CompanyName']}</h2>
                <p class="card-sub">{Lang::T('Enter Admin Area')}</p>

                <!-- Alert/Notification -->
                {if isset($notify)}
                <div class="alert-box {if $notify_t == 's'}alert-success{else}alert-error{/if}">
                    <i class="fa {if $notify_t == 's'}fa-circle-check{else}fa-circle-exclamation{/if}"></i>
                    <span>{$notify}</span>
                </div>
                {/if}

                <!-- Login Form -->
                <form action="{$_url}admin/post" method="post" id="loginForm" autocomplete="on">
                    <input type="hidden" name="csrf_token" value="{$csrf_token}">

                    <!-- Username -->
                    <div class="field-group">
                        <label class="field-label" for="username">
                            {Lang::T('Username')}
                        </label>
                        <div class="field-wrap">
                            <i class="fa fa-user field-icon"></i>
                            <input
                                id="username"
                                name="username"
                                type="text"
                                required
                                class="field-input"
                                placeholder="{Lang::T('Enter your username')}"
                                autocomplete="username"
                                spellcheck="false"
                            >
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="field-group">
                        <label class="field-label" for="password">
                            {Lang::T('Password')}
                        </label>
                        <div class="field-wrap">
                            <i class="fa fa-key field-icon"></i>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                class="field-input"
                                placeholder="{Lang::T('Enter your password')}"
                                autocomplete="current-password"
                                style="padding-right: 44px;"
                            >
                            <button type="button" class="toggle-pw" id="togglePw" tabindex="-1" title="Show/Hide Password">
                                <i class="fa fa-eye" id="togglePwIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Login Button -->
                    <button type="submit" class="btn-login" id="loginBtn">
                        <div class="btn-content">
                            <i class="fa fa-right-to-bracket"></i>
                            {Lang::T('Login')}
                        </div>
                        <div class="spinner">
                            <svg class="spin" width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" stroke="rgba(255,255,255,0.3)" stroke-width="3"/>
                                <path d="M12 2a10 10 0 0 1 10 10" stroke="white" stroke-width="3" stroke-linecap="round"/>
                            </svg>
                        </div>
                    </button>

                    <!-- Forgot Password -->
                    <a href="{$_url}admin/forgot-password" class="forgot-link">
                        <i class="fa fa-circle-question" style="margin-right:5px;"></i>
                        {Lang::T('Forgot Password')}?
                    </a>
                </form>

                <!-- Card Footer -->
                <div class="card-footer">
                    &copy; {$_c['CompanyName']} &mdash; Secure Admin Portal
                </div>
            </div>
        </div>
    </div>

    <script>
{literal}
        // ─── PARTICLES ───
        (function() {
            const container = document.getElementById('particles');
            const count = 28;
            for (let i = 0; i < count; i++) {
                const p = document.createElement('span');
                p.className = 'particle';
                const size = Math.random() * 3 + 1.5;
                p.style.cssText = `
                    left: ${Math.random() * 100}%;
                    width: ${size}px;
                    height: ${size}px;
                    animation-duration: ${Math.random() * 14 + 10}s;
                    animation-delay: ${Math.random() * 12}s;
                    opacity: ${Math.random() * 0.5 + 0.2};
                `;
                container.appendChild(p);
            }
        })();

        // ─── PASSWORD TOGGLE ───
        const togglePw   = document.getElementById('togglePw');
        const pwInput    = document.getElementById('password');
        const toggleIcon = document.getElementById('togglePwIcon');

        togglePw.addEventListener('click', function() {
            const isHidden = pwInput.type === 'password';
            pwInput.type = isHidden ? 'text' : 'password';
            toggleIcon.className = isHidden ? 'fa fa-eye-slash' : 'fa fa-eye';
        });

        // ─── FORM SUBMIT LOADING ───
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            btn.disabled = true;
            btn.classList.add('loading');
        });

        // ─── THEME TOGGLE ───
        const themeBtn  = document.getElementById('themeBtn');
        const themeIcon = document.getElementById('themeIcon');

        function applyTheme(theme) {
            if (theme === 'light') {
                document.body.classList.add('light-mode');
                themeIcon.className = 'fa fa-sun';
            } else {
                document.body.classList.remove('light-mode');
                themeIcon.className = 'fa fa-moon';
            }
        }

        // Initialize
        const saved = localStorage.getItem('adminTheme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        applyTheme(saved || (prefersDark ? 'dark' : 'light'));

        themeBtn.addEventListener('click', function() {
            const isLight = document.body.classList.contains('light-mode');
            const next = isLight ? 'dark' : 'light';
            applyTheme(next);
            localStorage.setItem('adminTheme', next);
        });

        // ─── INPUT FOCUS EFFECTS ───
        document.querySelectorAll('.field-input').forEach(input => {
            input.addEventListener('focus', () => {
                input.closest('.field-wrap').querySelector('.field-icon').style.color = '#818cf8';
            });
            input.addEventListener('blur', () => {
                input.closest('.field-wrap').querySelector('.field-icon').style.color = '';
            });
        });
{/literal}
    </script>
</body>
</html>
