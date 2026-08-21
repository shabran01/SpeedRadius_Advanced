{include file="customer/header-public.tpl"}

<style>
{literal}
/* Reset body background */
body.app.off-canvas.body-full {
    background: linear-gradient(135deg, #0f0c29 0%, #1a1040 40%, #0d1b3e 100%) !important;
    min-height: 100vh;
}

.container {
    background: transparent !important;
}

.form-head {
    display: none !important;
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.login-wrapper {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 420px;
    margin: 40px auto;
}

/* ─── LOGIN CARD ─── */
.login-card {
    width: 100%;
    background: rgba(20,20,50,0.8);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 20px;
    padding: 28px 24px;
    box-shadow:
        0 20px 40px rgba(0,0,0,0.4),
        0 0 0 1px rgba(255,255,255,0.1) inset;
    animation: cardIn 0.5s cubic-bezier(0.22, 1, 0.36, 1) both;
}

@keyframes cardIn {
    from { opacity: 0; transform: translateY(20px) scale(0.96); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}

/* ─── LOGO ─── */
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
    font-size: 1.4rem;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 8px;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.card-sub {
    text-align: center;
    font-size: 0.95rem;
    color: rgba(255,255,255,0.85);
    margin-bottom: 24px;
    font-weight: 500;
}

/* ─── FORM FIELDS ─── */
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
    color: #64748b;
    font-size: 14px;
    pointer-events: none;
}

.field-input {
    width: 100%;
    padding: 12px 12px 12px 40px;
    background: rgba(255,255,255,0.9);
    border: 1px solid rgba(255,255,255,0.4);
    border-radius: 10px;
    font-size: 0.95rem;
    font-family: inherit;
    color: #1e1b4b;
    outline: none;
    transition: all 0.2s ease;
    font-weight: 500;
}

.field-input::placeholder { 
    color: #64748b;
    font-weight: 400;
}

.field-input:focus {
    background: #ffffff;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.2);
}

.field-input:read-only {
    background: rgba(255,255,255,0.7);
    color: #475569;
}

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

.help-block {
    font-size: 0.8rem;
    color: rgba(255,255,255,0.8);
    margin-top: 10px;
    margin-bottom: 16px;
    line-height: 1.5;
}

/* ─── BUTTONS ─── */
.btn-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 18px;
}

.btn-group .btn {
    padding: 12px;
    border: none;
    border-radius: 10px;
    font-size: 0.9rem;
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

.btn-link {
    background: transparent;
    color: rgba(255,255,255,0.75);
    font-weight: 500;
}

.btn-link:hover {
    color: #818cf8;
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
    .card-sub { font-size: 0.8rem; margin-bottom: 16px; }
    
    .btn-group .btn { padding: 10px; font-size: 0.85rem; }
}
{/literal}
</style>

<div class="login-wrapper">
    <div class="login-card">
        <!-- Logo -->
        <div class="logo-box">
            <i class="fa fa-key"></i>
        </div>
        
        <h2 class="card-title">{$_c['CompanyName']}</h2>
        <p class="card-sub">
            {if $step == 1}
                {Lang::T('Verification Code')}
            {elseif $step == 2}
                {Lang::T('Success')}
            {elseif $step == 6}
                {Lang::T('Forgot Username')}
            {else}
                {Lang::T('Forgot Password')}
            {/if}
        </p>

        <!-- Alert Messages -->
        {if isset($msg)}
        <div class="alert-box alert-error">
            <i class="fa fa-circle-exclamation"></i>
            <span>{$msg}</span>
        </div>
        {/if}

        <!-- Forgot Form -->
        <form action="{$_url}forgot&step={$step+1}" method="post">
            {if $step == 1}
                <!-- Step 1: Verification Code -->
                <div class="field-group">
                    <label class="field-label">
                        {if $_c['country_code_phone']!= ''}{Lang::T('Phone Number')}{else}{Lang::T('Usernames')}{/if}
                    </label>
                    <div class="field-wrap">
                        <i class="fa fa-{if $_c['country_code_phone']!= ''}phone{else}user{/if} field-icon"></i>
                        <input type="text" readonly class="field-input" name="username" value="{$username}"
                            placeholder="{if $_c['country_code_phone']!= ''}{$_c['country_code_phone']} {Lang::T('Phone Number')}{else}{Lang::T('Usernames')}{/if}">
                    </div>
                </div>
                <div class="field-group">
                    <label class="field-label">{Lang::T('Verification Code')}</label>
                    <div class="field-wrap">
                        <i class="fa fa-asterisk field-icon"></i>
                        <input type="text" required class="field-input" id="otp_code"
                            placeholder="{Lang::T('Verification Code')}" name="otp_code">
                    </div>
                </div>
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">{Lang::T('Validate')}</button>
                    <a href="{$_url}forgot&step=-1" class="btn btn-link">{Lang::T('Cancel')}</a>
                </div>
            {elseif $step == 2}
                <!-- Step 2: Success -->
                <div class="field-group">
                    <label class="field-label">{if $_c['country_code_phone']!= ''}{Lang::T('Phone Number')}{else}{Lang::T('Usernames')}{/if}</label>
                    <div class="field-wrap">
                        <i class="fa fa-{if $_c['country_code_phone']!= ''}phone{else}user{/if} field-icon"></i>
                        <input type="text" readonly class="field-input" name="username" value="{$username}">
                    </div>
                </div>
                <div class="field-group">
                    <label class="field-label">{Lang::T('Your Password has been change to')}</label>
                    <div class="field-wrap">
                        <i class="fa fa-lock field-icon"></i>
                        <input type="text" readonly class="field-input" value="{$passsword}" onclick="this.select()">
                    </div>
                </div>
                <p class="help-block">
                    {Lang::T('Use the password to login, and change the password from password change page')}
                </p>
                <div class="btn-group">
                    <a href="{$_url}login" class="btn btn-primary">{Lang::T('Back to Login')}</a>
                </div>
            {elseif $step == 6}
                <!-- Step 6: Forgot Username -->
                <div class="field-group">
                    <label class="field-label">{Lang::T('Please input your Email or Phone number')}</label>
                    <div class="field-wrap">
                        <i class="fa fa-search field-icon"></i>
                        <input type="text" name="find" class="field-input" required value="" placeholder="{Lang::T('Email or Phone number')}">
                    </div>
                </div>
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">{Lang::T('Validate')}</button>
                    <a href="{$_url}forgot" class="btn btn-link">{Lang::T('Back')}</a>
                </div>
            {else}
                <!-- Default: Forgot Password -->
                <div class="field-group">
                    <label class="field-label">
                        {if $_c['country_code_phone']!= ''}{Lang::T('Phone Number')}{else}{Lang::T('Usernames')}{/if}
                    </label>
                    <div class="field-wrap">
                        <i class="fa fa-{if $_c['country_code_phone']!= ''}phone{else}user{/if} field-icon"></i>
                        <input type="text" class="field-input" name="username" required
                            placeholder="{if $_c['country_code_phone']!= ''}{$_c['country_code_phone']} {Lang::T('Phone Number')}{else}{Lang::T('Usernames')}{/if}">
                    </div>
                </div>
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">{Lang::T('Validate')}</button>
                    <a href="{$_url}forgot&step=6" class="btn btn-link">{Lang::T('Forgot Usernames')}</a>
                    <a href="{$_url}login" class="btn btn-link">{Lang::T('Back')}</a>
                </div>
            {/if}
        </form>
    </div>
</div>

{include file="customer/footer-public.tpl"}
