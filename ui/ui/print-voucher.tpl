<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{$_title}</title>
  <link rel="shortcut icon" type="image/x-icon" href="ui/ui/images/favicon.ico">
  <link rel="stylesheet" href="ui/ui/styles/bootstrap.min.css">
  <link rel="stylesheet" href="ui/ui/fonts/font-awesome/css/font-awesome.min.css">
  <style>
    html, body {
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
      color-adjust: exact;
    }
    * { box-sizing: border-box; }
    body { background: #f4f6f9; font-family: Arial, Helvetica, sans-serif; }

    .controls-panel { max-width: 900px; margin: 0 auto; padding: 16px; }
    .controls-panel h1 { font-size: 22px; font-weight: 700; color: #2b3d51; margin-bottom: 16px; }

    /* ===== A4 page container ===== */
    .page-a4 {
      background: white;
      width: 21cm;
      min-height: 29.7cm;
      margin: 0 auto 20px;
      padding: 0.8cm 0.6cm;
      box-shadow: 0 2px 12px rgba(0,0,0,.15);
      border-radius: 4px;
    }

    /* ===== Voucher grid ===== */
    .voucher-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      padding: 6px;
    }

    /* ===== Voucher card ===== */
    .voucher-card {
      flex: 0 0 calc(100% / {$vpl} - 9px);
      border-radius: 8px;
      overflow: hidden;
      background: #fff;
      box-shadow: 0 1px 4px rgba(0,0,0,0.08);
      page-break-inside: avoid;
      break-inside: avoid;
    }
    .voucher-accent { display: none; } /* accent now handled inside Voucher.html */
    .voucher-num {
      position: absolute;
      display: none; /* hidden, numbering now in card header */
    }
    .voucher-card-inner {
      padding: 0;
      font-size: 10px;
      line-height: 1.4;
    }
    .voucher-card-inner table { width: 100%; border-collapse: collapse; }
    .voucher-card-inner img { max-width: 90%; height: auto; display: block; margin: 0 auto; }

    /* ===== Page break indicator ===== */
    .page-break-indicator {
      display: flex; align-items: center; gap: 10px;
      margin: 12px 0; color: #7c3aed; font-size: 11px; font-weight: 600;
      text-transform: uppercase; letter-spacing: 0.05em;
    }
    .page-break-indicator::after {
      content: ''; flex: 1; height: 2px;
      background: linear-gradient(90deg, #7c3aed 0%, transparent 100%);
    }

    /* ================================================================
       PRINT STYLES
       ================================================================ */
    @media print {
      html, body {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        color-adjust: exact !important;
        background: white !important;
        margin: 0; padding: 0;
      }
      .no-print { display: none !important; }
      .page-a4 {
        margin: 0; padding: 0.4cm; box-shadow: none; border-radius: 0;
        width: 100%; min-height: auto;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }
      .voucher-card, .voucher-card * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        color-adjust: exact !important;
      }
    }

    @media screen and (max-width: 900px) { .page-a4 { width: 100%; padding: 8px; } }
  </style>
</head>
<body>

  <!-- ============================================================
       CONTROLS PANEL (hidden when printing)
       ============================================================ -->
  <div class="controls-panel no-print">
    <h1><i class="fa fa-ticket"></i> {Lang::T('Print Vouchers')}</h1>

    <form method="post" action="{$_url}plan/print-voucher/" id="controlsForm" class="panel panel-default">
      <div class="panel-body">
        <div class="row">
          <div class="col-sm-2">
            <div class="form-group">
              <label class="control-label">{Lang::T('From ID')}</label>
              <input type="text" name="from_id" value="{$from_id}" placeholder="0" class="form-control">
            </div>
          </div>
          <div class="col-sm-2">
            <div class="form-group">
              <label class="control-label">{Lang::T('Limit')}</label>
              <input type="text" name="limit" value="{$limit}" placeholder="40" class="form-control">
            </div>
          </div>
          <div class="col-sm-2">
            <div class="form-group">
              <label class="control-label">{Lang::T('Per Line')}</label>
              <input type="text" name="vpl" value="{$vpl}" placeholder="5" class="form-control">
            </div>
          </div>
          <div class="col-sm-2">
            <div class="form-group">
              <label class="control-label">{Lang::T('Break After')}</label>
              <input type="text" name="pagebreak" value="{$pagebreak}" placeholder="20" class="form-control">
            </div>
          </div>
          <div class="col-sm-2">
            <div class="form-group">
              <label class="control-label">{Lang::T('Plan')}</label>
              <select name="planid" class="form-control">
                <option value="0">{Lang::T('All Plans')}</option>
                {foreach $plans as $plan}
                  <option value="{$plan['id']}" {if $plan['id']==$planid}selected{/if}>{$plan['name_plan']}</option>
                {/foreach}
              </select>
            </div>
          </div>
          <div class="col-sm-2">
            <div class="form-group">
              <label class="control-label">&nbsp;</label>
              <button type="submit" id="generateBtn" class="btn btn-primary btn-block">
                <i class="fa fa-refresh"></i> {Lang::T('Generate')}
              </button>
            </div>
          </div>
        </div>
      </div>
    </form>

    <div class="panel panel-default" style="margin-bottom:10px;">
      <div class="panel-body" style="padding:8px 15px; display:flex; align-items:center; gap:16px; flex-wrap:wrap;">
        <button type="button" id="printBtn" class="btn btn-success">
          <i class="fa fa-print"></i> {Lang::T('Print Vouchers')}
        </button>
        <span>{Lang::T('Showing')} <strong>{$voucher|@count}</strong> {Lang::T('of')} <strong>{$vc}</strong> {Lang::T('vouchers')}</span>
        {if $v[0]['id']}
          <span>{Lang::T('From ID')}: <strong>{$v[0]['id']}</strong></span>
        {/if}
      </div>
    </div>
  </div>

  <!-- ============================================================
       VOUCHER PAGE
       ============================================================ -->
  <div class="page-a4" id="voucherPage">
    <div class="voucher-grid" id="voucherGrid">
      {$n = 1}
      {foreach $voucher as $vs}
        {$jml = $jml + 1}

        <!-- Single voucher card -->
        <div class="voucher-card">
          <div class="voucher-accent"></div>
          <div class="voucher-card-inner">
            {$vs}
          </div>
        </div>

        {if $n == $vpl}
          {$n = 1}
        {else}
          {$n = $n + 1}
        {/if}

        {* Page break *}
        {if $jml == $pagebreak}
          {$jml = 0}
          </div><!-- close voucher-grid -->
          <div class="page-break-indicator no-print"><span>{Lang::T('Page Break')}</span></div>
          <div class="voucher-grid">
        {/if}
      {/foreach}
    </div>
  </div>

  <!-- ============================================================
       SCRIPTS
       ============================================================ -->
  <script src="ui/ui/scripts/jquery.min.js"></script>
  {if isset($xfooter)}{$xfooter}{/if}
  <script>
  document.getElementById('printBtn').addEventListener('click', function() {
    window.print();
  });
  </script>
</body>
</html>