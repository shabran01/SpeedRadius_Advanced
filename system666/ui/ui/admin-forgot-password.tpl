<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{Lang::T('Forgot Password')} - {$_c['CompanyName']}</title>
    <meta name="description" content="Admin Password Reset - {$_c['CompanyName']}">
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
            align-items: center;
            justify-content: center;
            background: #0f0c29;
            padding: 20px;
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
            opacity: 0.35;
            pointer-events: none;
            z-index: 0;
            animation: floatOrb linear infinite;
        }
        .orb-1 { width: 400px; height: 400px; background: #6366f1; top: -150px; left: -100px; animation-duration: 18s; }
        .orb-2 { width: 350px; height: 350px; background: #06b6d4; bottom: -100px; right: -80px; animation-duration: 22s; animation-direction: reverse; }

        @keyframes floatOrb {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33%       { transform: translate(30px, -40px) scale(1.05); }
            66%       { transform: translate(-20px, 30px) scale(0.97); }
        }

        /* ─── CARD ─── */
        .card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 400px;
            background: rgba(255,255,255,0.06);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 24px;
            padding: 36px 32px;
            box-shadow:
                0 24px 48px rgba(0,0,0,0.3),
                0 0 0 1px rgba(255,255,255,0.05) inset,
                0 1px 0 rgba(255,255,255,0.15) inset;
            animation: cardIn 0.6s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(25px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ─── ICON ─── */
        .icon-box {
            width: 64px; height: 64px;
            border-radius: 18px;
            background: linear-gradient(135deg, #6366f1, #06b6d4);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 24px;
            color: #fff;
            box-shadow: 0 10px 24px rgba(99,102,241,0.35);
        }

        .icon-box.success {
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 10px 24px rgba(16,185,129,0.35);
        }

        .icon-box.set-pass {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            box-shadow: 0 10px 24px rgba(245,158,11,0.35);
        }

        /* ─── TITLE ─── */
        .card-title {
            text-align: center;
            font-size: 1.4rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 6px;
            letter-spacing: -0.3px;
        }

        .card-sub {
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 24px;
            line-height: 1.5;
        }

        /* ─── ALERT ─── */
        .alert-box {
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 0.85rem;
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

        /* ─── FORM ─── */
        .field-group { margin-bottom: 18px; }

        .field-label {
            display: block;
            font-size: 0.8rem;
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
            padding: 12px 14px 12px 42px;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 12px;
            font-size: 0.9rem;
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

        .field-input:read-only {
            background: rgba(255,255,255,0.05);
            cursor: default;
        }

        /* ─── BUTTON ─── */
        .btn-submit {
            width: 100%;
            padding: 13px;
            margin-top: 6px;
            border: none;
            border-radius: 12px;
            font-size: 0.925rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            color: #fff;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 50%, #06b6d4 100%);
            background-size: 200% 200%;
            background-position: 0% 50%;
            transition: background-position 0.4s ease, transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 6px 20px rgba(99,102,241,0.35);
        }

        .btn-submit:hover {
            background-position: 100% 50%;
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(99,102,241,0.45);
        }

        .btn-submit:active { transform: translateY(0); }

        .btn-submit .btn-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit.loading .btn-content { display: none; }
        .btn-submit .spinner { display: none; }
        .btn-submit.loading .spinner {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @keyframes spin { to { transform: rotate(360deg); } }
        .spin { animation: spin 0.8s linear infinite; }

        /* ─── BACK LINK ─── */
        .back-link {
            display: block;
            text-align: center;
            margin-top: 18px;
            font-size: 0.85rem;
            color: rgba(255,255,255,0.45);
            text-decoration: none;
            transition: color 0.2s;
        }

        .back-link:hover { color: #818cf8; }

        /* ─── PASSWORD DISPLAY ─── */
        .password-display {
            background: linear-gradient(135deg, rgba(16,185,129,0.15) 0%, rgba(6,182,212,0.1) 100%);
            border: 1px solid rgba(16,185,129,0.3);
            border-radius: 16px;
            padding: 24px 20px;
            text-align: center;
            margin-bottom: 24px;
            animation: fadeInUp 0.5s ease both;
            animation-delay: 0.2s;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .password-display label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: rgba(255,255,255,0.5);
            margin-bottom: 14px;
        }

        .password-display .password-value {
            display: inline-block;
            font-family: 'Courier New', monospace;
            font-size: 2.2rem;
            font-weight: 800;
            color: #6ee7b7;
            letter-spacing: 6px;
            padding: 14px 28px;
            background: rgba(0,0,0,0.25);
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid rgba(16,185,129,0.2);
            width: 100%;
            text-shadow: 0 0 20px rgba(110,231,183,0.3);
        }

        .password-display .password-value:hover {
            background: rgba(0,0,0,0.35);
            transform: scale(1.03);
            border-color: rgba(16,185,129,0.4);
            box-shadow: 0 0 24px rgba(16,185,129,0.15);
        }

        .password-hint {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.4);
            margin-top: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        /* ─── COPY BUTTON ─── */
        .copy-btn {
            background: linear-gradient(135deg, rgba(16,185,129,0.25) 0%, rgba(6,182,212,0.2) 100%);
            border: 1px solid rgba(16,185,129,0.35);
            border-radius: 12px;
            padding: 13px 20px;
            color: #6ee7b7;
            font-size: 0.9rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 0;
            margin-bottom: 12px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .copy-btn:hover {
            background: linear-gradient(135deg, rgba(16,185,129,0.4) 0%, rgba(6,182,212,0.3) 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16,185,129,0.2);
        }

        .copy-btn.copied {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #fff;
            border-color: #10b981;
        }

        /* ─── LOGIN BUTTON (step 2) ─── */
        .btn-login {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 12px;
            font-size: 0.925rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            color: #fff;
            cursor: pointer;
            text-decoration: none;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 50%, #06b6d4 100%);
            background-size: 200% 200%;
            background-position: 0% 50%;
            transition: background-position 0.4s ease, transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 6px 20px rgba(99,102,241,0.35);
        }

        .btn-login:hover {
            background-position: 100% 50%;
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(99,102,241,0.45);
            color: #fff;
            text-decoration: none;
        }

        /* ─── THEME TOGGLE ─── */
        .theme-toggle-btn {
            position: fixed;
            top: 18px;
            right: 18px;
            z-index: 100;
            width: 40px; height: 40px;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.15);
            background: rgba(255,255,255,0.07);
            backdrop-filter: blur(12px);
            color: rgba(255,255,255,0.7);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            transition: all 0.25s;
        }

        .theme-toggle-btn:hover {
            background: rgba(99,102,241,0.3);
            color: #fff;
            border-color: rgba(99,102,241,0.5);
            transform: scale(1.08);
        }

        /* ─── LIGHT MODE OVERRIDES ─── */
        body.light-mode { background: #f1f5f9; }

        body.light-mode .bg-mesh {
            background:
                radial-gradient(ellipse at 20% 50%, rgba(99,102,241,0.12) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(6,182,212,0.1) 0%, transparent 55%),
                linear-gradient(135deg, #e0e7ff 0%, #f0f9ff 50%, #faf5ff 100%);
        }

        body.light-mode .orb { opacity: 0.1; }

        body.light-mode .card {
            background: rgba(255,255,255,0.85);
            border-color: rgba(99,102,241,0.12);
            box-shadow: 0 20px 48px rgba(0,0,0,0.1);
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
        body.light-mode .back-link { color: #6366f1; }

        body.light-mode .alert-error  { background: rgba(239,68,68,0.08);  color: #dc2626; }
        body.light-mode .alert-success { background: rgba(16,185,129,0.08); color: #059669; }

        body.light-mode .theme-toggle-btn { background: rgba(99,102,241,0.1); color: #6366f1; border-color: rgba(99,102,241,0.2); }

        /* Light mode password display */
        body.light-mode .password-display {
            background: linear-gradient(135deg, rgba(16,185,129,0.08) 0%, rgba(6,182,212,0.06) 100%);
            border-color: rgba(16,185,129,0.25);
        }

        body.light-mode .password-display label {
            color: #64748b;
        }

        body.light-mode .password-display .password-value {
            background: rgba(16,185,129,0.08);
            color: #059669;
            border-color: rgba(16,185,129,0.2);
            text-shadow: none;
        }

        body.light-mode .password-display .password-value:hover {
            background: rgba(16,185,129,0.14);
        }

        body.light-mode .password-hint { color: #94a3b8; }

        body.light-mode .copy-btn {
            background: rgba(16,185,129,0.1);
            border-color: rgba(16,185,129,0.3);
            color: #059669;
        }

        body.light-mode .copy-btn:hover {
            background: rgba(16,185,129,0.2);
        }

        body.light-mode .copy-btn.copied {
            background: #10b981;
            color: #fff;
        }

        /* ─── RESPONSIVE ─── */
        @media (max-width: 480px) {
            body { padding: 12px; }
            .card { padding: 28px 20px; border-radius: 20px; max-width: 100%; }
            .card-title { font-size: 1.2rem; }
            .password-display .password-value { font-size: 1.6rem; letter-spacing: 4px; padding: 12px 16px; }
            .icon-box { width: 56px; height: 56px; font-size: 20px; }
        }

        @media (max-width: 360px) {
            .card { padding: 24px 16px; }
            .password-display .password-value { font-size: 1.3rem; letter-spacing: 3px; padding: 10px 12px; }
        }
{/literal}
    </style>
</head>

<body>
    <!-- Background -->
    <div class="bg-mesh"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <!-- Theme Toggle -->
    <button class="theme-toggle-btn" id="themeBtn" title="Toggle Light/Dark Mode">
        <i class="fa fa-moon" id="themeIcon"></i>
    </button>

    <div class="card">
        <!-- Icon -->
        <div class="icon-box {if $step == 3}success{elseif $step == 2}set-pass{/if}">
            <i class="fa {if $step == 3}fa-check{elseif $step == 2}fa-lock{else}fa-key{/if}"></i>
        </div>

        <!-- Title -->
        <h2 class="card-title">
            {if $step == 3}
                {Lang::T('Password Reset')}
            {elseif $step == 2}
                {Lang::T('Set New Password')}
            {elseif $step == 1}
                {Lang::T('Verify Code')}
            {else}
                {Lang::T('Forgot Password')}
            {/if}
        </h2>
        
        <!-- Subtitle -->
        <p class="card-sub">
            {if $step == 3}
                {Lang::T('Your password has been reset successfully')}
            {elseif $step == 2}
                {Lang::T('Choose a strong password for your account')}
            {elseif $step == 1}
                {Lang::T('Enter the verification code sent to your email')}
                {if !empty($_c['wa_url'])} {Lang::T('or WhatsApp')}{/if}
            {else}
                {Lang::T('Enter your username to receive a verification code via email')}
                {if !empty($_c['wa_url'])} {Lang::T('or WhatsApp')}{/if}
            {/if}
        </p>

        <!-- Alert -->
        {if isset($notify)}
        <div class="alert-box {if $notify_t == 's'}alert-success{else}alert-error{/if}">
            <i class="fa {if $notify_t == 's'}fa-circle-check{else}fa-circle-exclamation{/if}"></i>
            <span>{$notify}</span>
        </div>
        {/if}

        <!-- Form -->
        <form action="{$_url}admin/forgot-password&step={if $step == 2}3{else}{$step+1}{/if}" method="post" id="forgotForm">
            
            {if $step == 1 || $step == 2 || $step == 3}
            <!-- Username (shown in step 1, 2 & 3) -->
            <div class="field-group">
                <label class="field-label">{Lang::T('Username')}</label>
                <div class="field-wrap">
                    <i class="fa fa-user field-icon"></i>
                    <input type="text" name="username" class="field-input" value="{$username}" readonly>
                </div>
            </div>
            {/if}

            {if $step == 1}
            <!-- Verification Code -->
            <div class="field-group">
                <label class="field-label">{Lang::T('Verification Code')}</label>
                <div class="field-wrap">
                    <i class="fa fa-shield-halved field-icon"></i>
                    <input type="text" name="otp_code" class="field-input" placeholder="{Lang::T('Enter 6-digit code')}" required autocomplete="off">
                </div>
            </div>

            <button type="submit" class="btn-submit" id="submitBtn">
                <div class="btn-content">
                    <i class="fa fa-check-circle"></i>
                    {Lang::T('Verify')}
                </div>
                <div class="spinner">
                    <svg class="spin" width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="rgba(255,255,255,0.3)" stroke-width="3"/>
                        <path d="M12 2a10 10 0 0 1 10 10" stroke="white" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                </div>
            </button>

            {elseif $step == 2}
            <!-- Set New Password -->
            <input type="hidden" name="otp_code" value="{$otp_code}">
            
            <div class="field-group">
                <label class="field-label">{Lang::T('New Password')}</label>
                <div class="field-wrap">
                    <i class="fa fa-lock field-icon"></i>
                    <input type="password" name="new_password" id="new_password" class="field-input" placeholder="{Lang::T('Enter new password')}" required minlength="6" autocomplete="new-password">
                    <button type="button" class="toggle-pass" onclick="togglePassword('new_password', this)" title="Show/Hide">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="field-group">
                <label class="field-label">{Lang::T('Confirm Password')}</label>
                <div class="field-wrap">
                    <i class="fa fa-lock field-icon"></i>
                    <input type="password" name="confirm_password" id="confirm_password" class="field-input" placeholder="{Lang::T('Confirm new password')}" required minlength="6" autocomplete="new-password">
                    <button type="button" class="toggle-pass" onclick="togglePassword('confirm_password', this)" title="Show/Hide">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="password-requirements">
                <i class="fa fa-info-circle"></i>
                {Lang::T('Password must be at least 6 characters')}
            </div>

            <button type="submit" class="btn-submit" id="submitBtn">
                <div class="btn-content">
                    <i class="fa fa-save"></i>
                    {Lang::T('Reset Password')}
                </div>
                <div class="spinner">
                    <svg class="spin" width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="rgba(255,255,255,0.3)" stroke-width="3"/>
                        <path d="M12 2a10 10 0 0 1 10 10" stroke="white" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                </div>
            </button>

            {elseif $step == 3}
            <!-- Success -->
            <div class="success-box">
                <i class="fa fa-circle-check"></i>
                <p>{Lang::T('Your password has been changed successfully. You can now login with your new password.')}</p>
            </div>

            <a href="{$_url}admin" class="btn-login">
                <i class="fa fa-sign-in-alt"></i>
                {Lang::T('Login Now')}
            </a>

            {else}
            <!-- Step 0: Username Input -->
            <div class="field-group">
                <label class="field-label">{Lang::T('Username')}</label>
                <div class="field-wrap">
                    <i class="fa fa-user field-icon"></i>
                    <input type="text" name="username" class="field-input" placeholder="{Lang::T('Enter your admin username')}" required>
                </div>
            </div>

            <button type="submit" class="btn-submit" id="submitBtn">
                <div class="btn-content">
                    <i class="fa fa-paper-plane"></i>
                    {Lang::T('Send Code')}
                </div>
                <div class="spinner">
                    <svg class="spin" width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="rgba(255,255,255,0.3)" stroke-width="3"/>
                        <path d="M12 2a10 10 0 0 1 10 10" stroke="white" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                </div>
            </button>

            {if $step == 0}
            <div style="text-align: center; margin-top: 16px; font-size: 0.8rem; color: rgba(255,255,255,0.5);">
                <i class="fa fa-info-circle" style="margin-right: 5px;"></i>
                {Lang::T('Code will be sent to your registered email address and expires in 10 minutes')}
            </div>
            {/if}
            {/if}

            <!-- Back Link -->
            <a href="{$_url}admin" class="back-link">
                <i class="fa fa-arrow-left" style="margin-right: 5px;"></i>
                {Lang::T('Back to Login')}
            </a>
        </form>
    </div>

    <script>
{literal}
        // ─── FORM SUBMIT LOADING ───
        document.getElementById('forgotForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            if (btn) {
                btn.disabled = true;
                btn.classList.add('loading');
            }
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

        // ─── COPY PASSWORD ───
        function copyPassword() {
            const password = document.getElementById('newPass').innerText;
            const btn = document.getElementById('copyBtn');
            
            navigator.clipboard.writeText(password).then(function() {
                btn.innerHTML = '<i class="fa fa-check"></i> Copied!';
                btn.classList.add('copied');
                
                setTimeout(() => {
                    btn.innerHTML = '<i class="fa fa-copy"></i> Copy to Clipboard';
                    btn.classList.remove('copied');
                }, 2000);
            });
        }

        // ─── SELECT TEXT ───
        function selectText(elementId) {
            const element = document.getElementById(elementId);
            if (document.body.createTextRange) {
                const range = document.body.createTextRange();
                range.moveToElementText(element);
                range.select();
            } else if (window.getSelection) {
                const selection = window.getSelection();
                const range = document.createRange();
                range.selectNodeContents(element);
                selection.removeAllRanges();
                selection.addRange(range);
            }
        }
{/literal}
    </script>
</body>

</html>
