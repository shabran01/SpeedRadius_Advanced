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

.register-wrapper {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 500px;
    margin: 40px auto;
    padding: 0 16px;
}

/* ─── REGISTER CARD ─── */
.register-card {
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

/* ─── INFO SECTION ─── */
.info-section {
    background: rgba(99,102,241,0.1);
    border: 1px solid rgba(99,102,241,0.2);
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 20px;
    color: rgba(255,255,255,0.85);
    font-size: 0.85rem;
    line-height: 1.5;
}

.info-title {
    font-size: 0.8rem;
    font-weight: 700;
    color: #818cf8;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 8px;
}

/* ─── FORM SECTIONS ─── */
.form-section {
    margin-bottom: 24px;
}

.section-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: rgba(255,255,255,0.9);
    margin-bottom: 16px;
    padding-bottom: 8px;
    border-bottom: 1px solid rgba(255,255,255,0.15);
}

/* ─── FORM FIELDS ─── */
.field-group { margin-bottom: 14px; }

.field-label {
    display: block;
    font-size: 0.85rem;
    font-weight: 600;
    color: rgba(255,255,255,0.9);
    margin-bottom: 6px;
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

/* ─── PHOTO UPLOAD ─── */
.file-input-wrapper {
    position: relative;
    width: 100%;
}

.file-input {
    width: 100%;
    padding: 10px;
    background: rgba(255,255,255,0.9);
    border: 1px solid rgba(255,255,255,0.4);
    border-radius: 10px;
    font-size: 0.9rem;
    color: #1e1b4b;
    cursor: pointer;
}

/* ─── BUTTONS ─── */
.btn-group {
    display: flex;
    gap: 12px;
    margin-top: 24px;
}

.btn-group .btn {
    flex: 1;
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

.btn-secondary {
    background: rgba(245,158,11,0.2);
    border: 1px solid rgba(245,158,11,0.4) !important;
    color: #fbbf24;
}

.btn-secondary:hover {
    background: rgba(245,158,11,0.3);
}

/* ─── LINKS ─── */
.links-row {
    margin-top: 20px;
    text-align: center;
    font-size: 0.8rem;
}

.links-row a {
    color: rgba(255,255,255,0.7);
    text-decoration: none;
    transition: color 0.2s;
}

.links-row a:hover {
    color: #818cf8;
}

.links-row .divider {
    color: rgba(255,255,255,0.3);
    margin: 0 8px;
}

/* ─── RESPONSIVE DESIGN ─── */
@media (max-width: 480px) {
    .register-wrapper {
        margin: 20px auto;
        padding: 0 12px;
    }
    
    .register-card {
        padding: 24px 20px;
        border-radius: 16px;
    }
    
    .logo-box {
        width: 45px; height: 45px;
        font-size: 16px;
        margin-bottom: 12px;
    }
    
    .card-title { font-size: 1.2rem; }
    .card-sub { font-size: 0.85rem; margin-bottom: 20px; }
    
    .btn-group .btn { padding: 10px; font-size: 0.85rem; }
}
{/literal}
</style>

<div class="register-wrapper">
    <div class="register-card">
        <!-- Logo -->
        <div class="logo-box">
            <i class="fa fa-user-plus"></i>
        </div>
        
        <h2 class="card-title">{$_c['CompanyName']}</h2>
        <p class="card-sub">{Lang::T('Create New Account')}</p>

        <!-- Info Section -->
        <div class="info-section">
            <div class="info-title"><i class="fa fa-info-circle"></i> {Lang::T('Registration Info')}</div>
            {include file="$_path/../pages/Registration_Info.html"}
        </div>

        <!-- Register Form -->
        <form enctype="multipart/form-data" action="{$_url}register/post" method="post">
            
            <!-- Account Info Section -->
            <div class="form-section">
                <div class="section-title"><i class="fa fa-user"></i> {Lang::T('Account Information')}</div>
                
                <div class="field-group">
                    <label class="field-label">
                        {if $_c['registration_username'] == 'phone'}
                            {Lang::T('Phone Number')}
                        {elseif $_c['registration_username'] == 'email'}
                            {Lang::T('Email')}
                        {else}
                            {Lang::T('Usernames')}
                        {/if}
                    </label>
                    <div class="field-wrap">
                        <i class="fa fa-{if $_c['registration_username'] == 'phone'}phone{elseif $_c['registration_username'] == 'email'}envelope{else}user{/if} field-icon"></i>
                        <input type="text" class="field-input" name="username" required
                            placeholder="{if $_c['country_code_phone']!= '' || $_c['registration_username'] == 'phone'}{$_c['country_code_phone']} {Lang::T('Phone Number')}{elseif $_c['registration_username'] == 'email'}{Lang::T('Email')}{else}{Lang::T('Usernames')}{/if}">
                    </div>
                </div>

                {if $_c['photo_register'] == 'yes'}
                <div class="field-group">
                    <label class="field-label">{Lang::T('Photo')}</label>
                    <div class="file-input-wrapper">
                        <input type="file" required class="file-input" id="photo" name="photo" accept="image/*">
                    </div>
                </div>
                {/if}

                <div class="field-group">
                    <label class="field-label">{Lang::T('Full Name')}</label>
                    <div class="field-wrap">
                        <i class="fa fa-id-card field-icon"></i>
                        <input type="text" {if $_c['man_fields_fname'] neq 'no'}required{/if} class="field-input"
                            id="fullname" value="{$fullname}" name="fullname" placeholder="{Lang::T('Enter full name')}">
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label">{Lang::T('Email')}</label>
                    <div class="field-wrap">
                        <i class="fa fa-envelope field-icon"></i>
                        <input type="email" {if $_c['man_fields_email'] neq 'no'}required{/if} class="field-input"
                            id="email" placeholder="xxxxxxx@xxxx.xx" value="{$email}" name="email">
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label">{Lang::T('Home Address')}</label>
                    <div class="field-wrap">
                        <i class="fa fa-home field-icon"></i>
                        <input type="text" {if $_c['man_fields_address'] neq 'no'}required{/if} name="address"
                            id="address" value="{$address}" class="field-input" placeholder="{Lang::T('Enter address')}">
                    </div>
                </div>

                {$customFields}
            </div>

            <!-- Password Section -->
            <div class="form-section">
                <div class="section-title"><i class="fa fa-lock"></i> {Lang::T('Password')}</div>
                
                <div class="field-group">
                    <label class="field-label">{Lang::T('Password')}</label>
                    <div class="field-wrap">
                        <i class="fa fa-key field-icon"></i>
                        <input type="password" required class="field-input" id="password" name="password" placeholder="{Lang::T('Enter password')}">
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label">{Lang::T('Confirm Password')}</label>
                    <div class="field-wrap">
                        <i class="fa fa-key field-icon"></i>
                        <input type="password" required class="field-input" id="cpassword" name="cpassword" placeholder="{Lang::T('Confirm password')}">
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="btn-group">
                <a href="{$_url}login" class="btn btn-secondary">{Lang::T('Cancel')}</a>
                <button type="submit" class="btn btn-primary">{Lang::T('Register')}</button>
            </div>

            <!-- Links -->
            <div class="links-row">
                <a href="javascript:showPrivacy()">{Lang::T('Privacy')}</a>
                <span class="divider">•</span>
                <a href="javascript:showTaC()">{Lang::T('T & C')}</a>
            </div>
        </form>
    </div>
</div>

{include file="customer/footer-public.tpl"}