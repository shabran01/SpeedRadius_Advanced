{include file="customer/header.tpl"}
<style>
{literal}
/* -- MODERN CUSTOMER DASHBOARD v2.1.70 - 3D/HD Edition -- */
.cdr-wrap{padding:0 4px 56px;}

/* Hero */
.cdr-hero{
  background:linear-gradient(135deg,#1e40af 0%,#3b82f6 55%,#06b6d4 100%);
  border-radius:20px;padding:28px 28px 26px;margin-bottom:22px;
  position:relative;overflow:hidden;color:#fff;
  box-shadow:0 2px 0 rgba(255,255,255,.18) inset,
             0 12px 40px rgba(59,130,246,.55),
             0 4px 10px rgba(0,0,0,.18);
  border-top:1px solid rgba(255,255,255,.30);
}
.cdr-hero::before{content:'';position:absolute;top:-55px;right:-55px;width:210px;height:210px;background:rgba(255,255,255,.09);border-radius:50%;pointer-events:none;}
.cdr-hero::after{content:'';position:absolute;bottom:-75px;left:22%;width:270px;height:270px;background:rgba(255,255,255,.05);border-radius:50%;pointer-events:none;}
.cdr-hero-inner{position:relative;z-index:2;}
.cdr-hero-title{font-size:24px;font-weight:800;margin:0 0 5px;letter-spacing:-.5px;line-height:1.2;text-shadow:0 2px 6px rgba(0,0,0,.18);}
.cdr-hero-sub{font-size:13px;opacity:.82;margin:0 0 14px;}
.cdr-hero-badge{display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.18);backdrop-filter:blur(6px);border:1px solid rgba(255,255,255,.32);padding:5px 14px;border-radius:24px;font-size:12px;font-weight:700;letter-spacing:.2px;box-shadow:0 2px 8px rgba(0,0,0,.12);}
.cdr-hero-dot{width:7px;height:7px;border-radius:50%;background:#4ade80;display:inline-block;animation:cdr-pulse 1.6s infinite;box-shadow:0 0 6px #4ade80;}
@keyframes cdr-pulse{0%,100%{opacity:1;transform:scale(1);}50%{opacity:.45;transform:scale(1.5);}}

/* Alert */
.cdr-alert{
  background:linear-gradient(145deg,#fff 0%,#fefefe 100%);
  border-radius:16px;padding:16px 18px;margin-bottom:20px;
  box-shadow:0 1px 0 rgba(255,255,255,.9) inset,
             0 8px 24px rgba(0,0,0,.07),
             0 2px 4px rgba(0,0,0,.05);
  border:1px solid rgba(255,255,255,.8);
}
.cdr-alert-danger{
  border-left:5px solid #ef4444;
  box-shadow:0 1px 0 rgba(255,255,255,.9) inset,
             0 8px 24px rgba(239,68,68,.14),
             -4px 0 16px rgba(239,68,68,.08);
}
.cdr-alert-inner{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;}

/* Labels */
.cdr-lbl{font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.9px;color:#94a3b8;margin:0 0 10px 2px;display:flex;align-items:center;gap:5px;}
.cdr-lbl-mt{margin-top:6px;}

/* 3D Card - layered shadow + top-edge gloss */
.cdr-card{
  background:linear-gradient(170deg,#ffffff 0%,#f8faff 100%);
  border-radius:18px;padding:20px;margin-bottom:18px;
  border:1px solid rgba(255,255,255,.95);
  box-shadow:
    0 1px 0 rgba(255,255,255,1) inset,
    0 -1px 0 rgba(0,0,0,.04) inset,
    0 4px 6px rgba(0,0,0,.04),
    0 12px 28px rgba(59,130,246,.08),
    0 24px 48px rgba(0,0,0,.05);
  transition:transform .22s cubic-bezier(.34,1.56,.64,1),box-shadow .22s ease;
  will-change:transform;
}
.cdr-card:hover{
  transform:translateY(-4px) scale(1.005);
  box-shadow:
    0 1px 0 rgba(255,255,255,1) inset,
    0 -1px 0 rgba(0,0,0,.04) inset,
    0 8px 16px rgba(0,0,0,.07),
    0 20px 48px rgba(59,130,246,.14),
    0 32px 64px rgba(0,0,0,.07);
}
.cdr-card-hdr{display:flex;align-items:center;justify-content:space-between;padding-bottom:12px;margin-bottom:14px;border-bottom:2px solid rgba(241,245,249,.9);}
.cdr-card-title{font-size:15px;font-weight:700;color:#0f172a;margin:0;display:flex;align-items:center;gap:8px;}

/* 3D icon badges */
.cdr-icon{width:34px;height:34px;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0;box-shadow:0 2px 6px rgba(0,0,0,.10),0 1px 0 rgba(255,255,255,.7) inset;}
.cdr-icon-blue{background:linear-gradient(145deg,#dbeafe,#eff6ff);color:#3b82f6;}
.cdr-icon-green{background:linear-gradient(145deg,#bbf7d0,#f0fdf4);color:#10b981;}
.cdr-icon-orange{background:linear-gradient(145deg,#fed7aa,#fff7ed);color:#f59e0b;}
.cdr-icon-purple{background:linear-gradient(145deg,#ddd6fe,#f5f3ff);color:#8b5cf6;}

/* Rows */
.cdr-row{display:flex;align-items:center;justify-content:space-between;padding:9px 0;border-bottom:1px solid rgba(248,250,252,.9);gap:8px;}
.cdr-row:last-child{border-bottom:0;}
.cdr-row-lbl{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.3px;color:#94a3b8;flex-shrink:0;}
.cdr-row-val{font-size:13px;font-weight:600;color:#0f172a;text-align:right;word-break:break-word;min-width:0;}

/* Plan Card 3D - rich gradient + depth shadow */
.cdr-plan-card{
  background:linear-gradient(170deg,#ffffff 0%,#f0f4ff 100%);
  border-radius:18px;margin-bottom:18px;overflow:hidden;
  border:1px solid rgba(255,255,255,.95);
  box-shadow:
    0 1px 0 rgba(255,255,255,1) inset,
    0 4px 6px rgba(0,0,0,.04),
    0 12px 32px rgba(59,130,246,.10),
    0 24px 48px rgba(0,0,0,.06);
  transition:transform .22s cubic-bezier(.34,1.56,.64,1),box-shadow .22s ease;
  will-change:transform;
}
.cdr-plan-card:hover{
  transform:translateY(-5px) scale(1.006);
  box-shadow:
    0 1px 0 rgba(255,255,255,1) inset,
    0 8px 20px rgba(0,0,0,.08),
    0 24px 56px rgba(59,130,246,.18),
    0 40px 72px rgba(0,0,0,.08);
}
.cdr-plan-hdr{padding:16px 18px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;position:relative;overflow:hidden;}
.cdr-plan-hdr::after{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(255,255,255,.6),transparent);}
/* Blue active â€” vivid 4-stop gradient */
.cdr-ph-blue{background:linear-gradient(135deg,#1e3a8a 0%,#1d4ed8 40%,#3b82f6 75%,#60a5fa 100%);box-shadow:0 4px 16px rgba(29,78,216,.45) inset;}
/* Green */
.cdr-ph-green{background:linear-gradient(135deg,#064e3b 0%,#065f46 40%,#10b981 75%,#34d399 100%);box-shadow:0 4px 16px rgba(6,78,59,.45) inset;}
/* Gray expired */
.cdr-ph-gray{background:linear-gradient(135deg,#1f2937 0%,#374151 40%,#6b7280 75%,#9ca3af 100%);box-shadow:0 4px 16px rgba(31,41,55,.45) inset;}
.cdr-plan-name{font-size:15px;font-weight:700;color:#fff;margin:0;line-height:1.2;text-shadow:0 1px 3px rgba(0,0,0,.25);}
.cdr-plan-sub{font-size:11px;color:rgba(255,255,255,.76);margin-top:2px;}
.cdr-type-badge{font-size:11px;font-weight:700;padding:3px 10px;border-radius:12px;background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.35);color:#fff;white-space:nowrap;box-shadow:0 2px 6px rgba(0,0,0,.12);backdrop-filter:blur(4px);}
.cdr-plan-body{padding:14px 18px;}

/* Status badges */
.cdr-badge-on{display:inline-flex;align-items:center;gap:4px;background:linear-gradient(135deg,#bbf7d0,#dcfce7);color:#15803d;padding:2px 9px;border-radius:20px;font-size:11px;font-weight:700;box-shadow:0 1px 4px rgba(21,128,61,.15);}
.cdr-badge-off{display:inline-flex;align-items:center;gap:4px;background:linear-gradient(135deg,#fecaca,#fee2e2);color:#b91c1c;padding:2px 9px;border-radius:20px;font-size:11px;font-weight:700;box-shadow:0 1px 4px rgba(185,28,28,.15);}
.cdr-badge-ren-on{background:linear-gradient(135deg,#bbf7d0,#dcfce7);color:#15803d;padding:2px 8px;border-radius:8px;font-size:11px;font-weight:700;text-decoration:none!important;display:inline-block;box-shadow:0 1px 4px rgba(21,128,61,.15);}
.cdr-badge-ren-off{background:linear-gradient(135deg,#fecaca,#fee2e2);color:#b91c1c;padding:2px 8px;border-radius:8px;font-size:11px;font-weight:700;text-decoration:none!important;display:inline-block;box-shadow:0 1px 4px rgba(185,28,28,.15);}

/* Button row */
.cdr-btns{display:flex;gap:8px;flex-wrap:wrap;padding-top:12px;margin-top:10px;border-top:1.5px solid #f1f5f9;}

/* 3D Raised Buttons */
.cdr-btn{
  display:inline-flex;align-items:center;justify-content:center;gap:5px;
  padding:8px 16px;border-radius:11px;font-size:13px;font-weight:700;
  border:0;cursor:pointer;text-decoration:none!important;
  transition:transform .14s cubic-bezier(.34,1.56,.64,1),box-shadow .14s ease,filter .14s;
  line-height:1;position:relative;
}
.cdr-btn::after{content:'';position:absolute;top:0;left:10%;right:10%;height:45%;background:linear-gradient(180deg,rgba(255,255,255,.22),transparent);border-radius:6px 6px 0 0;pointer-events:none;}
.cdr-btn-sm{padding:6px 12px;font-size:12px;}
.cdr-btn-block{width:100%;box-sizing:border-box;}
.cdr-btn-p{background:linear-gradient(180deg,#4f95ff 0%,#1d4ed8 55%,#1740b0 100%);color:#fff!important;box-shadow:0 1px 0 rgba(255,255,255,.25) inset,0 4px 12px rgba(29,78,216,.45),0 2px 0 #1235a0;}
.cdr-btn-p:hover,.cdr-btn-p:focus{transform:translateY(-2px);filter:brightness(1.08);color:#fff!important;box-shadow:0 1px 0 rgba(255,255,255,.25) inset,0 8px 20px rgba(29,78,216,.50),0 2px 0 #1235a0;}
.cdr-btn-p:active{transform:translateY(1px);}
.cdr-btn-s{background:linear-gradient(180deg,#34d399 0%,#10b981 55%,#059669 100%);color:#fff!important;box-shadow:0 1px 0 rgba(255,255,255,.25) inset,0 4px 12px rgba(16,185,129,.40),0 2px 0 #047857;}
.cdr-btn-s:hover,.cdr-btn-s:focus{transform:translateY(-2px);filter:brightness(1.08);color:#fff!important;}
.cdr-btn-s:active{transform:translateY(1px);}
.cdr-btn-w{background:linear-gradient(180deg,#fbbf24 0%,#f59e0b 55%,#d97706 100%);color:#fff!important;box-shadow:0 1px 0 rgba(255,255,255,.25) inset,0 4px 12px rgba(245,158,11,.40),0 2px 0 #b45309;}
.cdr-btn-w:hover,.cdr-btn-w:focus{transform:translateY(-2px);filter:brightness(1.08);color:#fff!important;}
.cdr-btn-w:active{transform:translateY(1px);}
.cdr-btn-d{background:linear-gradient(180deg,#f87171 0%,#ef4444 55%,#dc2626 100%);color:#fff!important;box-shadow:0 1px 0 rgba(255,255,255,.25) inset,0 4px 12px rgba(239,68,68,.40),0 2px 0 #b91c1c;}
.cdr-btn-d:hover,.cdr-btn-d:focus{transform:translateY(-2px);filter:brightness(1.08);color:#fff!important;}
.cdr-btn-d:active{transform:translateY(1px);}
.cdr-btn-g{background:linear-gradient(180deg,#ffffff 0%,#f1f5f9 100%);color:#0f172a!important;border:1.5px solid #e2e8f0;box-shadow:0 1px 0 rgba(255,255,255,.9) inset,0 2px 6px rgba(0,0,0,.07),0 1px 0 #cbd5e1;}
.cdr-btn-g:hover,.cdr-btn-g:focus{background:linear-gradient(180deg,#f8fafc 0%,#e2e8f0 100%);transform:translateY(-1px);color:#0f172a!important;}
.cdr-btn-g:active{transform:translateY(1px);}

/* Quick Action grid */
.cdr-act-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.cdr-act-btn{
  display:flex;align-items:center;gap:10px;
  background:linear-gradient(160deg,#ffffff 0%,#f0f4ff 100%);
  border:1px solid rgba(255,255,255,.9);border-radius:14px;padding:14px;
  text-decoration:none!important;color:#0f172a!important;font-size:13px;font-weight:600;
  box-shadow:0 1px 0 rgba(255,255,255,1) inset,0 4px 12px rgba(59,130,246,.08),0 2px 4px rgba(0,0,0,.05);
  transition:transform .20s cubic-bezier(.34,1.56,.64,1),box-shadow .20s ease;
  will-change:transform;
}
.cdr-act-btn:hover,.cdr-act-btn:focus{
  background:linear-gradient(160deg,#eff6ff 0%,#dbeafe 100%);
  border-color:rgba(147,197,253,.7);
  transform:translateY(-3px) scale(1.02);
  box-shadow:0 1px 0 rgba(255,255,255,1) inset,0 8px 20px rgba(59,130,246,.16),0 4px 8px rgba(0,0,0,.07);
  color:#0f172a!important;
}
.cdr-act-icon{width:38px;height:38px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0;box-shadow:0 2px 6px rgba(0,0,0,.12),0 1px 0 rgba(255,255,255,.7) inset;}

/* Inputs */
.cdr-input{width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:13px;color:#0f172a;outline:none;background:linear-gradient(180deg,#f8fafc,#ffffff);box-sizing:border-box;transition:border-color .15s,box-shadow .15s;font-family:inherit;box-shadow:0 1px 3px rgba(0,0,0,.06) inset;}
.cdr-input:focus{border-color:#3b82f6;background:#fff;box-shadow:0 0 0 3px rgba(59,130,246,.10),0 1px 3px rgba(0,0,0,.05) inset;}
.cdr-fg{margin-bottom:12px;}
.cdr-fg label{font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.4px;display:block;margin-bottom:5px;}
.cdr-vrow{display:flex;gap:8px;align-items:center;}
.cdr-vrow .cdr-input{flex:1;}
.cdr-pass{width:100%;border:0;background:transparent;color:#0f172a;font-size:13px;font-weight:600;cursor:pointer;text-align:right;font-family:inherit;}

/* Announcement */
.cdr-announce{background:linear-gradient(135deg,#f0f9ff 0%,#f0fdf4 100%);border-radius:12px;padding:14px 16px;font-size:13px;color:#374151;line-height:1.7;box-shadow:0 1px 0 rgba(255,255,255,.9) inset,0 2px 8px rgba(59,130,246,.06);border:1px solid rgba(255,255,255,.8);}
.cdr-hr{border:0;border-top:1.5px solid #f1f5f9;margin:16px 0;}
.cdr-abill-total{display:flex;justify-content:space-between;align-items:center;background:linear-gradient(135deg,#f8fafc,#f1f5f9);border-radius:10px;padding:9px 12px;margin-top:6px;font-weight:700;color:#0f172a;font-size:13px;box-shadow:0 1px 0 rgba(255,255,255,.9) inset,0 1px 4px rgba(0,0,0,.05);}

/* Live Bandwidth card */
.cdr-bw-card{
  background:linear-gradient(160deg,#fdfbff 0%,#f5f0ff 100%);
  border-radius:18px;padding:20px 20px 14px;
  border:1px solid rgba(139,92,246,.15);border-top:4px solid #8b5cf6;
  box-shadow:0 1px 0 rgba(255,255,255,1) inset,0 8px 24px rgba(139,92,246,.14),0 20px 48px rgba(0,0,0,.06);
  margin-bottom:18px;
  transition:transform .22s cubic-bezier(.34,1.56,.64,1),box-shadow .22s ease;
}
.cdr-bw-card:hover{transform:translateY(-4px);box-shadow:0 1px 0 rgba(255,255,255,1) inset,0 16px 40px rgba(139,92,246,.22),0 28px 56px rgba(0,0,0,.08);}
.cdr-bw-badge{background:linear-gradient(135deg,#22c55e,#16a34a);color:#fff;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;display:inline-flex;align-items:center;gap:4px;box-shadow:0 2px 8px rgba(34,197,94,.40),0 1px 0 rgba(255,255,255,.25) inset;}
.cdr-bw-stat{border-radius:10px;padding:12px 10px;box-shadow:0 1px 0 rgba(255,255,255,.9) inset,0 2px 6px rgba(0,0,0,.06);border:1px solid rgba(255,255,255,.7);}

/* Monthly Usage card */
.cdr-usage-card{
  background:linear-gradient(160deg,#1e2434 0%,#141929 100%);
  border-radius:18px;padding:20px;
  border:1px solid rgba(255,255,255,.06);border-top:1px solid rgba(255,255,255,.10);
  box-shadow:0 1px 0 rgba(255,255,255,.05) inset,0 12px 40px rgba(0,0,0,.35),0 4px 12px rgba(0,0,0,.20);
  margin-bottom:18px;
  transition:transform .22s cubic-bezier(.34,1.56,.64,1),box-shadow .22s ease;
}
.cdr-usage-card:hover{transform:translateY(-4px);box-shadow:0 1px 0 rgba(255,255,255,.05) inset,0 20px 56px rgba(0,0,0,.45),0 8px 20px rgba(0,0,0,.25);}

/* Responsive */
@media(max-width:767px){
  .cdr-hero{padding:20px 16px 18px;}
  .cdr-hero-title{font-size:19px;}
  .cdr-act-grid{grid-template-columns:1fr;}
  .cdr-btns .cdr-btn{min-width:0;}
  .cdr-wrap{padding:0 0px 40px;}
}
{/literal}
</style>

<div class="cdr-wrap">

{* -- Hero Greeting -- *}
<div class="cdr-hero">
  <div class="cdr-hero-inner">
    <p class="cdr-hero-title">&#128075; Hello, {$_user['username']}</p>
    <p class="cdr-hero-sub">{$_c['CompanyName']} &mdash; Customer Portal</p>
    {if $_bills}
      {foreach $_bills as $_hb}
        {if $_hb['status'] == 'on'}
          <span class="cdr-hero-badge">
            <span class="cdr-hero-dot"></span> Active &bull; {$_hb['namebp']}
          </span>
          {break}
        {/if}
      {/foreach}
    {/if}
  </div>
</div>

{* -- Unpaid Order Alert -- *}
{if $unpaid}
<div class="cdr-alert cdr-alert-danger">
  <div class="cdr-alert-inner">
    <div>
      <div style="font-size:14px;font-weight:700;color:#dc2626;margin-bottom:5px;">
        <i class="fa fa-exclamation-circle"></i> {Lang::T('Unpaid Order')}
      </div>
      <div style="font-size:13px;color:#374151;">
        <b>{$unpaid['plan_name']}</b> &nbsp;&bull;&nbsp; {Lang::moneyFormat($unpaid['price'])} &nbsp;&bull;&nbsp; {$unpaid['routers']}
      </div>
      <div style="font-size:12px;color:#94a3b8;margin-top:3px;">
        {Lang::T('expired')}: {Lang::dateTimeFormat($unpaid['expired_date'])}
      </div>
    </div>
    <div style="display:flex;gap:8px;flex-shrink:0;">
      <a href="{$_url}order/view/{$unpaid['id']}/cancel"
         class="cdr-btn cdr-btn-sm cdr-btn-d"
         onclick="return ask(this, '{Lang::T('Cancel it?')}')">
        <i class="fa fa-trash"></i> {Lang::T('Cancel')}
      </a>
      <a href="{$_url}order/view/{$unpaid['id']}" class="cdr-btn cdr-btn-sm cdr-btn-s">
        <i class="ion ion-card"></i> {Lang::T('PAY NOW')}
      </a>
    </div>
  </div>
</div>
{/if}

<div class="row">

{* -- LEFT COLUMN -- *}
<div class="col-md-6">

<div class="cdr-lbl"><i class="fa fa-user-circle-o"></i>{Lang::T('Account Information')}</div>
<div class="cdr-card">
  <div class="cdr-card-hdr">
    <div class="cdr-card-title">
      <span class="cdr-icon cdr-icon-blue"><i class="fa fa-id-card-o"></i></span>
      {Lang::T('Your Account')}
    </div>
  </div>
  <div class="cdr-row">
    <span class="cdr-row-lbl">{Lang::T('Username')}</span>
    <span class="cdr-row-val" style="font-family:monospace;font-size:14px;color:#3b82f6;font-weight:700;">{$_user['username']}</span>
  </div>
  <div class="cdr-row">
    <span class="cdr-row-lbl">{Lang::T('Password')}</span>
    <span class="cdr-row-val">
      <input type="password" value="{$_user['password']}" class="cdr-pass"
        onmouseenter="this.type='text'" onmouseleave="this.type='password'" onclick="this.select()">
    </span>
  </div>
  <div class="cdr-row">
    <span class="cdr-row-lbl">{Lang::T('Service Type')}</span>
    <span class="cdr-row-val">
      {if $_user.service_type == 'Hotspot'}
        <span class="cdr-badge-on"><i class="fa fa-wifi"></i> Hotspot</span>
      {elseif $_user.service_type == 'PPPoE'}
        <span class="cdr-badge-on"><i class="fa fa-plug"></i> PPPoE</span>
      {else}
        <span style="color:#64748b;font-weight:600;">Others</span>
      {/if}
    </span>
  </div>
  {if $_c['enable_balance'] == 'yes'}
  <div class="cdr-row">
    <span class="cdr-row-lbl">{Lang::T('Balance')}</span>
    <span class="cdr-row-val">
      <span style="color:#10b981;font-size:15px;font-weight:700;">{Lang::moneyFormat($_user['balance'])}</span>
      &nbsp;
      {if $_user['auto_renewal'] == 1}
        <a class="cdr-badge-ren-on" href="{$_url}home&renewal=0"
           onclick="return ask(this, '{Lang::T('Disable auto renewal?')}')">{Lang::T('Auto Renewal On')}</a>
      {else}
        <a class="cdr-badge-ren-off" href="{$_url}home&renewal=1"
           onclick="return ask(this, '{Lang::T('Enable auto renewal?')}')">{Lang::T('Auto Renewal Off')}</a>
      {/if}
    </span>
  </div>
  {/if}
  {if $nux_ip neq ''}
  <div class="cdr-row">
    <span class="cdr-row-lbl">{Lang::T('Current IP')}</span>
    <span class="cdr-row-val" style="font-family:monospace;">{$nux_ip}</span>
  </div>
  {/if}
  {if $nux_mac neq ''}
  <div class="cdr-row">
    <span class="cdr-row-lbl">{Lang::T('Current MAC')}</span>
    <span class="cdr-row-val" style="font-family:monospace;font-size:12px;">{$nux_mac}</span>
  </div>
  {/if}
</div>

{* Additional Billing *}
{if $abills && count($abills) > 0}
<div class="cdr-card">
  <div class="cdr-card-hdr">
    <div class="cdr-card-title">
      <span class="cdr-icon cdr-icon-orange"><i class="fa fa-file-text-o"></i></span>
      {Lang::T('Additional Billing')}
    </div>
  </div>
  {assign var="total" value=0}
  {foreach $abills as $k => $v}
  <div class="cdr-row">
    <span class="cdr-row-lbl">{str_replace(' Bill', '', $k)}</span>
    <span class="cdr-row-val">
      {if strpos($v, ':') === false}
        {Lang::moneyFormat($v)} <sup title="recurring" style="color:#f59e0b;">&#8734;</sup>
        {assign var="total" value=$v+$total}
      {else}
        {assign var="exp" value=explode(':',$v)}
        {Lang::moneyFormat($exp[0])}
        <sup title="{$exp[1]} more times">{if $exp[1]==0}{Lang::T('paid off')}{else}{$exp[1]}x{/if}</sup>
        {if $exp[1]>0}{assign var="total" value=$exp[0]+$total}{/if}
      {/if}
    </span>
  </div>
  {/foreach}
  <div class="cdr-abill-total">
    <span>{Lang::T('Total')}</span>
    <span style="color:#10b981;">
      {if $total==0}{ucwords(Lang::T('paid off'))}{else}{Lang::moneyFormat($total)}{/if}
    </span>
  </div>
</div>
{/if}

{* Active Plan Cards *}
{if $_bills}
<div class="cdr-lbl cdr-lbl-mt"><i class="fa fa-signal"></i>{Lang::T('Active Plans')}</div>
{foreach $_bills as $_bill}
<div class="cdr-plan-card">
  <div class="cdr-plan-hdr {if $_bill['status']=='on'}cdr-ph-blue{else}cdr-ph-gray{/if}">
    <div>
      {if $_bill['routers'] != 'radius'}
        <div class="cdr-plan-name">{$_bill['routers']}</div>
        <div class="cdr-plan-sub">
          {if $_bill['type'] == 'Hotspot'}
            {if $_c['hotspot_plan']==''}{Lang::T('Hotspot Plan')}{else}{$_c['hotspot_plan']}{/if}
          {else if $_bill['type'] == 'PPPOE'}
            {if $_c['pppoe_plan']==''}{Lang::T('PPPOE Plan')}{else}{$_c['pppoe_plan']}{/if}
          {/if}
        </div>
      {else}
        <div class="cdr-plan-name">
          {if $_c['radius_plan']==''}{Lang::T('Radius Plan')}{else}{$_c['radius_plan']}{/if}
        </div>
      {/if}
    </div>
    {if $_bill['status'] == 'on'}
      <span class="cdr-type-badge"><i class="fa fa-check-circle"></i> Active</span>
    {else}
      <span class="cdr-type-badge" style="background:rgba(239,68,68,.25);border-color:rgba(239,68,68,.4);">
        <i class="fa fa-times-circle"></i> {Lang::T('Expired')}
      </span>
    {/if}
  </div>
  <div class="cdr-plan-body">
    <div class="cdr-row">
      <span class="cdr-row-lbl">{Lang::T('Package Name')}</span>
      <span class="cdr-row-val">{$_bill['namebp']}</span>
    </div>
    {if $_c['show_bandwidth_plan'] == 'yes'}
    <div class="cdr-row">
      <span class="cdr-row-lbl">{Lang::T('Bandwidth')}</span>
      <span class="cdr-row-val">{$_bill['name_bw']}</span>
    </div>
    {/if}
    <div class="cdr-row">
      <span class="cdr-row-lbl">{Lang::T('Created On')}</span>
      <span class="cdr-row-val">
        {if $_bill['time'] ne ''}{Lang::dateAndTimeFormat($_bill['recharged_on'],$_bill['recharged_time'])}{/if}&nbsp;
      </span>
    </div>
    <div class="cdr-row">
      <span class="cdr-row-lbl">{Lang::T('Expires On')}</span>
      <span class="cdr-row-val" style="color:#ef4444;">
        {if $_bill['time'] ne ''}{Lang::dateAndTimeFormat($_bill['expiration'],$_bill['time'])}{/if}&nbsp;
      </span>
    </div>
    <div class="cdr-row">
      <span class="cdr-row-lbl">{Lang::T('Type')}</span>
      <span class="cdr-row-val">
        <b>{if $_bill['prepaid'] eq yes}Prepaid{else}Postpaid{/if}</b>
        {if $_bill['plan_type']} &middot; {$_bill['plan_type']}{/if}
      </span>
    </div>
    {if $cf}
    {foreach $cf as $tcf}
      {if $tcf['field_name'] == 'Winbox' or $tcf['field_name'] == 'Api' or $tcf['field_name'] == 'Web'}
      <div class="cdr-row">
        <span class="cdr-row-lbl">{$tcf['field_name']} Port</span>
        <span class="cdr-row-val">
          <a href="http://{$vpn['public_ip']}:{$tcf['field_value']}" target="_blank">{$tcf['field_value']}</a>
        </span>
      </div>
      {/if}
    {/foreach}
    {/if}
    {if $_bill['type'] == 'Hotspot' && $_bill['status'] == 'on' && $_bill['routers'] != 'radius' && $_c['hs_auth_method'] != 'hchap'}
    <div class="cdr-row">
      <span class="cdr-row-lbl">{Lang::T('Login Status')}</span>
      <span class="cdr-row-val" id="login_status_{$_bill['id']}">
        <img src="ui/ui/images/loading.gif" style="height:16px;">
      </span>
    </div>
    {/if}
    {if $_bill['type'] == 'Hotspot' && $_bill['status'] == 'on' && $_c['hs_auth_method'] == 'hchap'}
    <div class="cdr-row">
      <span class="cdr-row-lbl">{Lang::T('Login Status')}</span>
      <span class="cdr-row-val">
        {if $logged == '1'}
          <a href="http://{$hostname}/status" class="cdr-btn cdr-btn-sm cdr-btn-s">{Lang::T('You are Online')}</a>
        {else}
          <a href="{$_url}home&mikrotik=login"
             onclick="return ask(this, '{Lang::T('Connect to Internet')}')"
             class="cdr-btn cdr-btn-sm cdr-btn-d">{Lang::T('Not Online, Login now?')}</a>
        {/if}
      </span>
    </div>
    {/if}
    <div class="cdr-btns">
      {if $_bill['status'] != 'on' && $_bill['prepaid'] != 'yes' && $_c['extend_expired']}
        <a class="cdr-btn cdr-btn-sm cdr-btn-w"
           href="{$_url}home&extend={$_bill['id']}&stoken={App::getToken()}"
           onclick="return ask(this, '{Text::toHex($_c['extend_confirmation'])}')">{Lang::T('Extend')}</a>
      {/if}
      <a class="cdr-btn cdr-btn-sm cdr-btn-p"
         href="{$_url}home&recharge={$_bill['id']}&stoken={App::getToken()}"
         onclick="return ask(this, '{Lang::T('Recharge')}?')">
        <i class="fa fa-refresh"></i> {Lang::T('Recharge')}
      </a>
      <a class="cdr-btn cdr-btn-sm cdr-btn-g"
         href="{$_url}home&sync={$_bill['id']}&stoken={App::getToken()}"
         onclick="return ask(this, '{Lang::T('Sync account if you failed login to internet')}?')"
         title="{Lang::T('Sync account if you failed login to internet')}">
        <i class="fa fa-refresh"></i> {Lang::T('Sync')}
      </a>
      {if $_bill['status'] == 'on' && $_bill['prepaid'] != 'YES'}
        <a href="{$_url}home&deactivate={$_bill['id']}"
           onclick="return ask(this, '{Lang::T('Deactivate')}?')"
           class="cdr-btn cdr-btn-sm cdr-btn-d">
          <i class="fa fa-trash"></i>
        </a>
      {/if}
    </div>
  </div>
</div>
{/foreach}
{/if}

</div>{* end col-md-6 left *}

{* -- RIGHT COLUMN -- *}
<div class="col-md-6">

<div class="cdr-lbl"><i class="fa fa-bolt"></i>{Lang::T('Quick Actions')}</div>
<div class="cdr-card">
  <div class="cdr-act-grid">
    {if $_c['payment_gateway'] != 'none' || $_c['payment_gateway'] == ''}
    <a href="{$_url}order/package" class="cdr-act-btn">
      <span class="cdr-act-icon" style="background:linear-gradient(145deg,#dbeafe,#eff6ff);color:#3b82f6;"><i class="ion ion-ios-cart"></i></span>
      {Lang::T('Order Package')}
    </a>
    {/if}
    {if $_c['disable_voucher'] != 'yes'}
    <a href="{$_url}voucher/activation" class="cdr-act-btn">
      <span class="cdr-act-icon" style="background:linear-gradient(145deg,#bbf7d0,#f0fdf4);color:#10b981;"><i class="fa fa-ticket"></i></span>
      {Lang::T('Order Voucher')}
    </a>
    {/if}
    {if $_c['enable_balance'] == 'yes' && $_c['allow_balance_transfer'] == 'yes'}
    <a href="#cdr-balance" class="cdr-act-btn">
      <span class="cdr-act-icon" style="background:linear-gradient(145deg,#fed7aa,#fff7ed);color:#f59e0b;"><i class="fa fa-exchange"></i></span>
      {Lang::T('Transfer Balance')}
    </a>
    {/if}
    {if $_c['disable_voucher'] != 'yes'}
    <a href="#cdr-voucher" class="cdr-act-btn">
      <span class="cdr-act-icon" style="background:linear-gradient(145deg,#ddd6fe,#f5f3ff);color:#8b5cf6;"><i class="fa fa-qrcode"></i></span>
      {Lang::T('Voucher Activation')}
    </a>
    {/if}
  </div>
</div>

<div class="cdr-lbl"><i class="fa fa-bullhorn"></i>{Lang::T('Announcement')}</div>
<div class="cdr-card">
  <div class="cdr-announce">
    {$Announcement_Customer = "{$PAGES_PATH}/Announcement_Customer.html"}
    {if file_exists($Announcement_Customer)}
      {include file=$Announcement_Customer}
    {else}
      <span style="color:#94a3b8;font-style:italic;">{Lang::T('No announcements at this time.')}</span>
    {/if}
  </div>
</div>

{if $_c['disable_voucher'] != 'yes'}
<div id="cdr-voucher" class="cdr-lbl"><i class="fa fa-key"></i>{Lang::T('Voucher Activation')}</div>
<div class="cdr-card">
  <form method="post" role="form" action="{$_url}voucher/activation-post">
    <div class="cdr-fg">
      <label>{Lang::T('Enter Voucher Code')}</label>
      <div class="cdr-vrow">
        <a class="cdr-btn cdr-btn-sm cdr-btn-g"
           href="{APP_URL}/scan/?back={urlencode($_url)}{urlencode('home&code=')}">
          <i class="fa fa-qrcode"></i>
        </a>
        <input type="text" id="code" name="code" class="cdr-input"
               placeholder="{Lang::T('Enter voucher code here')}" value="{$code}">
        <button class="cdr-btn cdr-btn-sm cdr-btn-p" type="submit" style="flex-shrink:0;">
          {Lang::T('Activate')}
        </button>
      </div>
    </div>
  </form>
</div>
{/if}

{if $_c['enable_balance'] == 'yes' && $_c['allow_balance_transfer'] == 'yes'}
<div id="cdr-balance" class="cdr-lbl"><i class="fa fa-exchange"></i>{Lang::T('Transfer Balance')}</div>
<div class="cdr-card">
  <form method="post" onsubmit="return cdrAskBalance()" role="form" action="{$_url}home">
    <div class="cdr-fg">
      <label>{Lang::T('Friend Username')}</label>
      <input type="text" name="username" class="cdr-input" required placeholder="{Lang::T('Friend Usernames')}">
    </div>
    <div class="cdr-fg">
      <label>{Lang::T('Balance Amount')}</label>
      <input type="number" name="balance" autocomplete="off" class="cdr-input" required placeholder="{Lang::T('Balance Amount')}">
    </div>
    <button class="cdr-btn cdr-btn-s cdr-btn-block" id="cdrBalBtn" type="submit"
            name="send" value="balance"
            onclick="return ask(this, '{Lang::T('Are You Sure?')}')">
      <i class="fa fa-paper-plane"></i> {Lang::T('Send Balance')}
    </button>
  </form>
  <script>
  function cdrAskBalance() {
    if (confirm('{Lang::T('Send yours balance ? ')}')) {
      setTimeout(function() {
        document.getElementById('cdrBalBtn').setAttribute('disabled', '');
      }, 1000);
      return true;
    }
    return false;
  }
  </script>
  <hr class="cdr-hr">
  <div class="cdr-lbl" style="margin-top:0;"><i class="fa fa-gift"></i>{Lang::T('Recharge a Friend')}</div>
  <form method="post" role="form" action="{$_url}home">
    <div class="cdr-fg">
      <label>{Lang::T('Username')}</label>
      <input type="text" name="username" class="cdr-input" required placeholder="{Lang::T('Usernames')}">
    </div>
    <button class="cdr-btn cdr-btn-p cdr-btn-block" type="submit"
            name="send" value="plan"
            onclick="return ask(this, '{Lang::T('Are You Sure?')}')">
      <i class="fa fa-wifi"></i> {Lang::T('Recharge Friend')}
    </button>
  </form>
</div>
{/if}

</div>{* end col-md-6 right *}
</div>{* end .row *}

{* -- hchap auto-login -- *}
{if isset($hostname) && $hchap == 'true' && $_c['hs_auth_method'] == 'hchap'}
    <script type="text/javascript" src="/ui/ui/scripts/md5.js"></script>
    <script type="text/javascript">
        var hostname = "http://{$hostname}/login";
        var user = "{$_user['username']}";
        var pass = "{$_user['password']}";
        var dst = "{$apkurl}";
        var authdly = "2";
        var key = hexMD5('{$key1}' + pass + '{$key2}');
        var auth = hostname + '?username=' + user + '&dst=' + dst + '&password=' + key;
        document.write('<meta http-equiv="refresh" target="_blank" content="' + authdly + '; url=' + auth + '">');
    </script>
{/if}

{* -- isLogin AJAX -- *}
{if $_bills}
    {foreach $_bills as $_bill}
        {if $_bill['type'] == 'Hotspot' && $_bill['status'] == 'on' && $_c['hs_auth_method'] != 'hchap'}
            <script>
                setTimeout(function() {
                    $.ajax({
                        url: "?_route=autoload_user/isLogin/{$_bill['id']}",
                        cache: false,
                        success: function(msg) {
                            $("#login_status_{$_bill['id']}").html(msg);
                        }
                    });
                }, 2000);
            </script>
        {/if}
    {/foreach}
{/if}

{* -- Live Bandwidth -- *}
{if $_bills}
    {foreach $_bills as $_bw}
        {if $_bw['status'] == 'on' && $_bw['routers'] != 'radius' && ($_bw['type'] == 'Hotspot' || $_bw['type'] == 'PPPOE' || $_bw['type'] == 'PPPoE')}
<div class="cdr-bw-card" style="margin-top:10px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:6px;">
        <h4 style="margin:0;font-weight:700;color:#1e2434;font-size:16px;display:flex;align-items:center;gap:8px;">
            <span style="background:linear-gradient(145deg,#ddd6fe,#f5f3ff);color:#8b5cf6;width:30px;height:30px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;font-size:14px;box-shadow:0 2px 6px rgba(139,92,246,.20),0 1px 0 rgba(255,255,255,.7) inset;"><i class="fa fa-line-chart"></i></span>
            {Lang::T('Live Bandwidth')}
        </h4>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            <span class="cdr-bw-badge">
                <span style="width:6px;height:6px;background:#fff;border-radius:50%;display:inline-block;opacity:.85;"></span>
                {$_bw['type']} &bull; <span id="bw-ip-{$_bw['id']}">...</span> &bull; <span id="bw-up-{$_bw['id']}">...</span>
            </span>
            <span id="bw-pause-btn-{$_bw['id']}" style="cursor:pointer;font-size:18px;line-height:1;color:#94a3b8;" title="{Lang::T('Pause/Resume')}">&#9208;</span>
        </div>
    </div>
    <div class="row" style="margin-bottom:14px;">
        <div class="col-xs-4">
            <div class="cdr-bw-stat" style="background:#f0fdf4;">
                <div style="font-size:10px;color:#16a34a;font-weight:700;text-transform:uppercase;margin-bottom:4px;">&#11015; {Lang::T('Download')}</div>
                <div id="bw-dl-{$_bw['id']}" style="font-size:20px;font-weight:700;color:#111;">--</div>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="cdr-bw-stat" style="background:#eff6ff;">
                <div style="font-size:10px;color:#2563eb;font-weight:700;text-transform:uppercase;margin-bottom:4px;">&#11014; {Lang::T('Upload')}</div>
                <div id="bw-ul-{$_bw['id']}" style="font-size:20px;font-weight:700;color:#111;">--</div>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="cdr-bw-stat" style="background:#fff7ed;">
                <div style="font-size:10px;color:#ea580c;font-weight:700;text-transform:uppercase;margin-bottom:4px;">&#8644; {Lang::T('Session DL')}</div>
                <div id="bw-tdl-{$_bw['id']}" style="font-size:20px;font-weight:700;color:#111;">--</div>
            </div>
        </div>
    </div>
    <div style="position:relative;height:150px;">
        <canvas id="bw-canvas-{$_bw['id']}"></canvas>
    </div>
    <div style="font-size:11px;color:#94a3b8;margin-top:8px;display:flex;align-items:center;gap:6px;">
        <span style="width:7px;height:7px;background:#22c55e;border-radius:50%;display:inline-block;animation:cdr-pulse 1.6s infinite;"></span>
        {Lang::T('Updates every 3 seconds')} &mdash; {Lang::T('Live')}
    </div>
</div>
<script>
(function() {
    var billId = {$_bw['id']};
{literal}
    var MAX_PTS = 40;
    var labels = [], dlData = [], ulData = [];
    var lastRx = null, lastTx = null, lastTs = null;
    var sessionRxStart = null;
    var paused = false;
    var chart = null;
    function fmtSpeed(bps) {
        if (bps >= 1e6)  return (bps / 1e6).toFixed(2)  + ' Mbps';
        if (bps >= 1e3)  return (bps / 1e3).toFixed(2)  + ' Kbps';
        return bps.toFixed(0) + ' bps';
    }
    function fmtBytes(b) {
        b = Math.max(0, b);
        if (b >= 1073741824) return (b / 1073741824).toFixed(2) + ' GB';
        if (b >= 1048576)    return (b / 1048576).toFixed(2)    + ' MB';
        if (b >= 1024)       return (b / 1024).toFixed(2)       + ' KB';
        return b + ' B';
    }
    function nowStr() {
        var d = new Date();
        return ('0'+d.getHours()).slice(-2)+':'+('0'+d.getMinutes()).slice(-2)+':'+('0'+d.getSeconds()).slice(-2);
    }
    function initChart() {
        var ctx = document.getElementById('bw-canvas-' + billId);
        if (!ctx || chart) return;
        chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    { label: 'Download', data: dlData, borderColor: '#22c55e', backgroundColor: 'rgba(34,197,94,0.10)', borderWidth: 2, pointRadius: 0, fill: true, tension: 0.4 },
                    { label: 'Upload',   data: ulData, borderColor: '#60a5fa', backgroundColor: 'rgba(96,165,250,0.10)', borderWidth: 2, pointRadius: 0, fill: true, tension: 0.4 }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false, animation: false,
                scales: {
                    y: { beginAtZero: true, ticks: { callback: function(v){
                            if (v >= 1e6) return (v/1e6).toFixed(1) + ' Mbps';
                            if (v >= 1e3) return (v/1e3).toFixed(0) + ' Kbps';
                            return v + ' bps';
                        }, font: {size:10} } },
                    x: { ticks: { maxTicksLimit: 6, font: {size:9} } }
                },
                plugins: { legend: { labels: { font: {size:11}, boxWidth:12 } } }
            }
        });
    }
    function poll() {
        if (paused) return;
        $.ajax({
            url: '?_route=autoload_user/live_bandwidth/' + billId,
            cache: false, dataType: 'json',
            success: function(d) {
                if (!d || d.error) return;
                var now = Date.now();
                document.getElementById('bw-ip-' + billId).textContent = d.ip || '--';
                document.getElementById('bw-up-' + billId).textContent = d.uptime || '--';
                if (sessionRxStart === null) sessionRxStart = d.rx_bytes;
                document.getElementById('bw-tdl-' + billId).textContent = fmtBytes(d.rx_bytes - sessionRxStart);
                if (lastRx !== null && lastTs !== null) {
                    var dt = (now - lastTs) / 1000;
                    if (dt > 0) {
                        var dlBps = Math.max(0, d.rx_bytes - lastRx) * 8 / dt;
                        var ulBps = Math.max(0, d.tx_bytes - lastTx) * 8 / dt;
                        document.getElementById('bw-dl-' + billId).textContent = fmtSpeed(dlBps);
                        document.getElementById('bw-ul-' + billId).textContent = fmtSpeed(ulBps);
                        if (labels.length >= MAX_PTS) { labels.shift(); dlData.shift(); ulData.shift(); }
                        labels.push(nowStr());
                        dlData.push(Math.round(dlBps));
                        ulData.push(Math.round(ulBps));
                        if (chart) chart.update();
                    }
                }
                lastRx = d.rx_bytes; lastTx = d.tx_bytes; lastTs = now;
            }
        });
    }
    document.getElementById('bw-pause-btn-' + billId).addEventListener('click', function() {
        paused = !paused;
        this.innerHTML = paused ? '&#9654;' : '&#9208;';
    });
    function start() {
        if (typeof Chart === 'undefined') { setTimeout(start, 300); return; }
        initChart(); poll(); setInterval(poll, 3000);
    }
    start();
})();
{/literal}
</script>
        {/if}
    {/foreach}
{/if}

{* -- Monthly Data Usage -- *}
{if $monthly_usage && count($monthly_usage) > 0}
<div class="cdr-usage-card" style="margin-top:10px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:6px;">
        <h4 style="margin:0;font-weight:700;color:#fff;font-size:16px;display:flex;align-items:center;gap:8px;">
            <span style="background:rgba(255,255,255,.1);width:30px;height:30px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,.20),0 1px 0 rgba(255,255,255,.08) inset;"><i class="fa fa-bar-chart"></i></span>
            {Lang::T('Monthly Data Usage')}
        </h4>
        <span style="font-size:12px;color:#64748b;">{Lang::T('Resets on the 1st of every month')}</span>
    </div>
    {if $monthly_usage[0]['is_current']}
    <div class="row" style="margin-bottom:16px;">
        <div class="col-xs-4">
            <div style="background:rgba(74,222,128,0.12);border-radius:10px;padding:14px 10px;box-shadow:0 1px 0 rgba(255,255,255,.05) inset,0 2px 8px rgba(0,0,0,.15);border:1px solid rgba(74,222,128,.12);">
                <div style="font-size:10px;color:#4ade80;font-weight:700;text-transform:uppercase;margin-bottom:4px;">&#11015; {Lang::T('Downloaded')}</div>
                <div style="font-size:20px;font-weight:700;color:#4ade80;">{$monthly_usage[0]['download']}</div>
            </div>
        </div>
        <div class="col-xs-4">
            <div style="background:rgba(96,165,250,0.12);border-radius:10px;padding:14px 10px;box-shadow:0 1px 0 rgba(255,255,255,.05) inset,0 2px 8px rgba(0,0,0,.15);border:1px solid rgba(96,165,250,.12);">
                <div style="font-size:10px;color:#60a5fa;font-weight:700;text-transform:uppercase;margin-bottom:4px;">&#11014; {Lang::T('Uploaded')}</div>
                <div style="font-size:20px;font-weight:700;color:#60a5fa;">{$monthly_usage[0]['upload']}</div>
            </div>
        </div>
        <div class="col-xs-4">
            <div style="background:rgba(251,146,60,0.12);border-radius:10px;padding:14px 10px;box-shadow:0 1px 0 rgba(255,255,255,.05) inset,0 2px 8px rgba(0,0,0,.15);border:1px solid rgba(251,146,60,.12);">
                <div style="font-size:10px;color:#fb923c;font-weight:700;text-transform:uppercase;margin-bottom:4px;">&#8644; {Lang::T('Total')}</div>
                <div style="font-size:20px;font-weight:700;color:#fb923c;">{$monthly_usage[0]['total']}</div>
            </div>
        </div>
    </div>
    {/if}
    <div style="background:#111827;border-radius:10px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.25) inset;">
        <table class="table table-condensed" style="margin:0;color:#e2e8f0;">
            <thead>
                <tr style="background:#0f172a;">
                    <th style="color:#94a3b8;font-size:11px;text-transform:uppercase;font-weight:600;border:0;padding:9px 12px;">{Lang::T('Month')}</th>
                    <th style="color:#4ade80;font-size:11px;text-transform:uppercase;font-weight:600;border:0;padding:9px 12px;">&#11015; {Lang::T('Download')}</th>
                    <th style="color:#60a5fa;font-size:11px;text-transform:uppercase;font-weight:600;border:0;padding:9px 12px;">&#11014; {Lang::T('Upload')}</th>
                    <th style="color:#fb923c;font-size:11px;text-transform:uppercase;font-weight:600;border:0;padding:9px 12px;">&#8644; {Lang::T('Total')}</th>
                    <th style="color:#94a3b8;font-size:11px;text-transform:uppercase;font-weight:600;border:0;padding:9px 12px;">{Lang::T('Updated')}</th>
                </tr>
            </thead>
            <tbody>
                {foreach $monthly_usage as $mu}
                <tr style="border-color:#1f2937;{if $mu['is_current']}background:rgba(250,204,21,0.06);{/if}">
                    <td style="border-color:#1f2937;vertical-align:middle;padding:9px 12px;">
                        {if $mu['is_current']}<span style="background:#fbbf24;color:#1e2434;padding:2px 7px;border-radius:10px;font-size:10px;font-weight:700;margin-right:5px;">NOW</span>{/if}
                        <span style="color:#e2e8f0;">{$mu['month_label']}</span>
                    </td>
                    <td style="color:#4ade80;font-weight:600;border-color:#1f2937;padding:9px 12px;">{$mu['download']}</td>
                    <td style="color:#60a5fa;font-weight:600;border-color:#1f2937;padding:9px 12px;">{$mu['upload']}</td>
                    <td style="color:#fb923c;font-weight:600;border-color:#1f2937;padding:9px 12px;">{$mu['total']}</td>
                    <td style="color:#64748b;font-size:11px;border-color:#1f2937;padding:9px 12px;">{$mu['last_updated']}</td>
                </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
</div>
{/if}

</div>{* end .cdr-wrap *}

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
{include file="customer/footer.tpl"}
