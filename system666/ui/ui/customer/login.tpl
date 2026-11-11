{include file="customer/header-public.tpl"}

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
    --text-white: #f8fafc;
    --text-muted: rgba(255,255,255,0.6);
}

body.customer-login {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #0f0c29 0%, #1a1040 40%, #0d1b3e 100%);
    padding: 16px;
}

/* ─── LOGIN CONTAINER (more compact) ─── */
.login-wrapper {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 420px;
    margin: 0 auto;
}

/* ─── LOGIN CARD (smaller and more compact) ─── */
.login-card {
    width: 100%;
    background: rgba(255,255,255,0.08);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 20px;
    padding: 28px 24px;
    box-shadow:
        0 20px 40px rgba(0,0,0,0.25),
        0 0 0 1px rgba(255,255,255,0.08) inset;
    animation: cardIn 0.5s cubic-bezier(0.22, 1, 0.36, 1) both;
}

/* ─── ANIMATED BACKGROUND ─── */
.bg-mesh {
    position: fixed;
    inset: 0;
    z-index: 0;
    background:
        radial-gradient(ellipse at 20% 50%, rgba(99,102,241,0.25) 0%, transparent 50%),
        radial-gradient(ellipse at 80% 20%, rgba(6,182,212,0.2) 0%, transparent 45%),
        radial-gradient(ellipse at 60% 80%, rgba(139,92,246,0.2) 0%, transparent 45%),
        linear-gradient(135deg, #0f0c29 0%, #1a1040 40%, #0d1b3e 100%);
    animation: meshShift 15s ease-in-out infinite alternate;
}

@keyframes meshShift {
    0%   { filter: hue-rotate(0deg) brightness(1); }
    100% { filter: hue-rotate(20deg) brightness(1.05); }
}

/* ─── FLOATING ORBS (reduced size) ─── */
.orb {
    position: fixed;
    border-radius: 50%;
    filter: blur(60px);
    opacity: 0.25;
    pointer-events: none;
    z-index: 0;
    animation: floatOrb linear infinite;
}
.orb-1 { width: 300px; height: 300px; background: #6366f1; top: -100px; left: -80px; animation-duration: 20s; }
.orb-2 { width: 250px; height: 250px; background: #06b6d4; bottom: -80px; right: -60px; animation-duration: 25s; animation-direction: reverse; }

@keyframes floatOrb {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33%       { transform: translate(20px, -30px) scale(1.03); }
    66%       { transform: translate(-15px, 20px) scale(0.98); }
}

/* ─── LOGIN CONTAINER (more compact) ─── */
.login-wrapper {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 420px;
    margin: 0 auto;
}

/* ─── LOGIN CARD (smaller and more compact) ─── */
.login-card {
    width: 100%;
    background: rgba(255,255,255,0.08);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 20px;
    padding: 28px 24px;
    box-shadow:
        0 20px 40px rgba(0,0,0,0.25),
        0 0 0 1px rgba(255,255,255,0.08) inset;
    animation: cardIn 0.5s cubic-bezier(0.22, 1, 0.36, 1) both;
}

@keyframes cardIn {
    from { opacity: 0; transform: translateY(20px) scale(0.96); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}

/* ─── LOGO (smaller) ─── */
.logo-box {
    width: 50px; height: 50px;
    border-radius: 14px;
    background: linear-gradient(135deg, #6366f1, #06b6d4);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 14px;
    font-size: 18px;
    color: #fff;
    box-shadow: 0 8px 20px rgba(99,102,241,0.3);
}

.card-title {
    text-align: center;
    font-size: 1.2rem;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 3px;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.card-sub {
    text-align: center;
    font-size: 0.85rem;
    color: rgba(255,255,255,0.85);
    margin-bottom: 20px;
    font-weight: 500;
}

/* ─── WELCOME SECTION (compact) ─── */
.welcome-section {
    background: rgba(255,255,255,0.06);
    border-radius: 16px;
    padding: 16px;
    margin-top: 20px;
    text-align: center;
    border-top: 1px solid rgba(255,255,255,0.15);
}

.welcome-title {
    font-size: 1rem;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 8px;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.welcome-title span {
    background: linear-gradient(90deg, #818cf8, #06b6d4);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.welcome-desc {
    font-size: 0.8rem;
    color: rgba(255,255,255,0.8);
    line-height: 1.4;
    font-weight: 400;
}

/* ─── FEATURES (compact horizontal layout) ─── */
.features-row {
    display: flex;
    justify-content: space-around;
    margin-top: 12px;
    gap: 8px;
}

.feature-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    font-size: 0.75rem;
    color: rgba(255,255,255,0.8);
    font-weight: 500;
}

.feature-icon {
    width: 28px; height: 28px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}
.fi-1 { background: rgba(99,102,241,0.25); color: #818cf8; }
.fi-2 { background: rgba(6,182,212,0.25); color: #22d3ee; }
.fi-3 { background: rgba(16,185,129,0.25); color: #34d399; }

/* ─── ALERT ─── */
.alert-box {
    border-radius: 10px;
    padding: 10px 14px;
    margin-bottom: 16px;
    display: flex;
    align-items: flex-start;
    gap: 8px;
    font-size: 0.8rem;
    font-weight: 500;
    animation: alertIn 0.3s ease;
}
@keyframes alertIn {
    from { opacity: 0; transform: translateY(-6px); }
    to   { opacity: 1; transform: translateY(0); }
}
.alert-error {
    background: rgba(239,68,68,0.15);
    border: 1px solid rgba(239,68,68,0.3);
    color: #fca5a5;
}
.alert-success {
    background: rgba(16,185,129,0.15);
    border: 1px solid rgba(16,185,129,0.3);
    color: #6ee7b7;
}

/* ─── FORM FIELDS (more compact) ─── */
.field-group { margin-bottom: 14px; }

.field-label {
    display: block;
    font-size: 0.8rem;
    font-weight: 600;
    color: rgba(255,255,255,0.9);
    margin-bottom: 6px;
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
}

.field-wrap {
    position: relative;
    display: flex;
    align-items: center;
}

.field-icon {
    position: absolute;
    left: 12px;
    color: rgba(255,255,255,0.5);
    font-size: 13px;
    pointer-events: none;
}

.field-input {
    width: 100%;
    padding: 10px 12px 10px 38px;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 10px;
    font-size: 0.9rem;
    font-family: inherit;
    color: #ffffff;
    outline: none;
    transition: all 0.2s ease;
    font-weight: 500;
}

.field-input::placeholder { 
    color: rgba(255,255,255,0.4);
    font-weight: 400;
}

.field-input:focus {
    background: rgba(255,255,255,0.15);
    border-color: rgba(99,102,241,0.7);
    box-shadow: 0 0 0 2px rgba(99,102,241,0.2);
}

/* ─── BUTTONS (more compact) ─── */
.btn-group-justified {
    display: flex;
    gap: 10px;
    margin-top: 18px;
}

.btn-group-justified .btn {
    flex: 1;
    padding: 10px;
    border: none;
    border-radius: 10px;
    font-size: 0.85rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    text-align: center;
}

.btn-primary {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 50%, #06b6d4 100%);
    background-size: 200% 200%;
    background-position: 0% 50%;
    color: #fff;
    box-shadow: 0 4px 15px rgba(99,102,241,0.3);
}

.btn-primary:hover {
    background-position: 100% 50%;
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(99,102,241,0.4);
}

.btn-success {
    background: rgba(16,185,129,0.15);
    border: 1px solid rgba(16,185,129,0.35) !important;
    color: #6ee7b7;
}

.btn-success:hover {
    background: rgba(16,185,129,0.25);
}

/* ─── LINKS ─── */
.card-links {
    margin-top: 16px;
    text-align: center;
}

.card-links a {
    font-size: 0.75rem;
    color: rgba(255,255,255,0.4);
    text-decoration: none;
    transition: color 0.2s;
}

.card-links a:hover { color: #818cf8; }

/* ─── ANNOUNCEMENT (compact) ─── */
.announcement-box {
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 12px;
    padding: 12px;
    margin-bottom: 16px;
    color: rgba(255,255,255,0.85);
    font-size: 0.8rem;
    line-height: 1.4;
    font-weight: 400;
}

.announcement-title {
    font-size: 0.75rem;
    font-weight: 700;
    color: #818cf8;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 6px;
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
}

/* ─── RESPONSIVE DESIGN ─── */
@media (max-width: 480px) {
    body.customer-login { padding: 12px; }
    
    .login-card {
        padding: 24px 20px;
        border-radius: 16px;
    }
    
    .logo-box {
        width: 45px; height: 45px;
        font-size: 16px;
        margin-bottom: 12px;
    }
    
    .card-title { font-size: 1.1rem; }
    .card-sub { font-size: 0.75rem; margin-bottom: 16px; }
    
    .welcome-section { 
        padding: 14px; 
        margin-top: 16px;
    }
    .features-row { gap: 6px; }
    .feature-item { font-size: 0.65rem; }
    .feature-icon { width: 24px; height: 24px; font-size: 10px; }
    
    .btn-group-justified { gap: 8px; margin-top: 16px; }
    .btn-group-justified .btn { padding: 9px; font-size: 0.8rem; }
}

/* ─── LIGHT MODE ─── */
body.light-mode { background: #f1f5f9; }

body.light-mode .bg-mesh {
    background:
        radial-gradient(ellipse at 20% 50%, rgba(99,102,241,0.08) 0%, transparent 50%),
        radial-gradient(ellipse at 80% 20%, rgba(6,182,212,0.06) 0%, transparent 45%),
        linear-gradient(135deg, #e0e7ff 0%, #f0f9ff 50%, #faf5ff 100%);
}

body.light-mode .orb { opacity: 0.08; }

body.light-mode .login-card {
    background: rgba(255,255,255,0.9);
    border-color: rgba(99,102,241,0.1);
    box-shadow: 0 15px 35px rgba(0,0,0,0.08);
}

body.light-mode .card-title { color: #1e1b4b; }
body.light-mode .card-sub   { color: #64748b; }
body.light-mode .field-label { color: #475569; }

body.light-mode .field-input {
    background: rgba(241,245,249,0.7);
    border-color: #e2e8f0;
    color: #1e1b4b;
}

body.light-mode .field-input::placeholder { color: #94a3b8; }

body.light-mode .field-input:focus {
    background: #fff;
    border-color: #6366f1;
}

body.light-mode .field-icon { color: #94a3b8; }
body.light-mode .card-links a { color: #6366f1; }

body.light-mode .welcome-section {
    background: rgba(99,102,241,0.04);
}

body.light-mode .welcome-title { color: #1e1b4b; }
body.light-mode .welcome-desc { color: #64748b; }
body.light-mode .feature-item { color: #64748b; }

body.light-mode .announcement-box {
    background: rgba(99,102,241,0.04);
    border-color: rgba(99,102,241,0.08);
    color: #475569;
}
{/literal}
</style>

<!-- Background -->
<div class="bg-mesh"></div>
<div class="orb orb-1"></div>
<div class="orb orb-2"></div>

<div class="login-wrapper">
    <div class="login-card">
        <!-- Logo and Title -->
        <div class="logo-box">
            <i class="fa fa-user"></i>
        </div>
        
        <h2 class="card-title">{$_c['CompanyName']}</h2>
        <p class="card-sub">{Lang::T('Member Login')}</p>

        <!-- Alert Messages -->
        {if isset($msg)}
        <div class="alert-box alert-error">
            <i class="fa fa-circle-exclamation"></i>
            <span>{$msg}</span>
        </div>
        {/if}

        <!-- Login Form -->
        <form action="{$_url}login/post" method="post" id="loginForm">
            <input type="hidden" name="csrf_token" value="{$csrf_token}">
            
            <!-- Username -->
            <div class="field-group">
                <label class="field-label">
                    {if $_c['registration_username'] == 'phone'}
                        {Lang::T('Phone Number')}
                    {elseif $_c['registration_username'] == 'email'}
                        {Lang::T('Email')}
                    {else}
                        {Lang::T('Username')}
                    {/if}
                </label>
                <div class="field-wrap">
                    <i class="fa fa-{if $_c['registration_username'] == 'phone'}phone{elseif $_c['registration_username'] == 'email'}envelope{else}user{/if} field-icon"></i>
                    <input type="text" name="username" class="field-input" 
                        placeholder="{if $_c['country_code_phone']!= '' || $_c['registration_username'] == 'phone'}{$_c['country_code_phone']} {Lang::T('Phone Number')}{elseif $_c['registration_username'] == 'email'}{Lang::T('Email')}{else}{Lang::T('Username')}{/if}"
                        required autocomplete="username">
                </div>
            </div>

            <!-- Password -->
            <div class="field-group">
                <label class="field-label">{Lang::T('Password')}</label>
                <div class="field-wrap">
                    <i class="fa fa-key field-icon"></i>
                    <input type="password" name="password" class="field-input" 
                        placeholder="{Lang::T('Enter password')}" required autocomplete="current-password">
                </div>
            </div>

            <!-- Buttons -->
            <div class="btn-group-justified">
                {if $_c['disable_registration'] != 'noreg'}
                <a href="{$_url}register" class="btn btn-success">
                    <i class="fa fa-user-plus"></i> {Lang::T('Register')}
                </a>
                {/if}
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-sign-in-alt"></i> {Lang::T('Login')}
                </button>
            </div>

            <!-- Links -->
            <div class="card-links">
                <a href="{$_url}forgot"><i class="fa fa-key"></i> {Lang::T('Forgot Password')}</a>
            </div>
        </form>

        <!-- Welcome Section (moved to bottom) -->
        <div class="welcome-section">
            <div class="welcome-title">
                Welcome to<br>
                <span>{$_c['CompanyName']}</span>
            </div>
            <p class="welcome-desc">
                Access your member dashboard to manage your internet service, view plans, and more.
            </p>
            
            {assign var="Announcement" value=$PAGES_PATH|cat:"/Announcement.html"}
            {if file_exists($Announcement)}
            <div class="announcement-box">
                <div class="announcement-title"><i class="fa fa-bullhorn"></i> Announcement</div>
                {include file=$Announcement}
            </div>
            {/if}

            <div class="features-row">
                <div class="feature-item">
                    <div class="feature-icon fi-1"><i class="fa fa-wifi"></i></div>
                    High-Speed
                </div>
                <div class="feature-item">
                    <div class="feature-icon fi-2"><i class="fa fa-chart-line"></i></div>
                    Analytics
                </div>
                <div class="feature-item">
                    <div class="feature-icon fi-3"><i class="fa fa-headset"></i></div>
                    24/7 Support
                </div>
            </div>
        </div>
    </div>
</div>

{include file="customer/footer-public.tpl"}
