<?php
/* Smarty version 4.5.3, created on 2025-08-17 12:33:16
  from '/var/www/html/ISP/ui/ui/invoice.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_68a1a1dc8e45d6_46233419',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5733f62d5352eb08f5c1904468fadb520775b289' => 
    array (
      0 => '/var/www/html/ISP/ui/ui/invoice.tpl',
      1 => 1754916133,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:sections/header.tpl' => 1,
    'file:sections/footer.tpl' => 1,
  ),
),false)) {
function content_68a1a1dc8e45d6_46233419 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:sections/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<?php if (!$_smarty_tpl->tpl_vars['in']->value) {?>
    <div class="alert alert-danger">
        <h4>Error: Invoice data not found</h4>
        <p>The invoice data could not be loaded. Please check if the invoice ID is correct.</p>
        <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/list" class="btn btn-primary">Back to Plan List</a>
    </div>
<?php } else { ?>

<style>
.modern-invoice {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    overflow: hidden;
    margin: 10px auto;
    max-width: 210mm; /* A4 width */
    min-height: 297mm; /* A4 height */
}

.invoice-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px;
    position: relative;
}

.invoice-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255,255,255,0.05);
}

.company-logo {
    width: 45px;
    height: 45px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 8px;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255,255,255,0.3);
}

.invoice-title {
    font-size: 16px;
    font-weight: 300;
    margin: 0;
    letter-spacing: 0.5px;
}

.invoice-id {
    font-size: 13px;
    opacity: 0.9;
    font-weight: 500;
    margin-top: 4px;
}

.invoice-body {
    padding: 18px;
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 18px;
}

.info-section h4 {
    color: #667eea;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 12px;
    border-bottom: 1px solid #f0f0f0;
    padding-bottom: 6px;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 8px 0;
    border-bottom: 1px solid #f8f9fa;
    min-height: 30px;
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: #495057;
    font-size: 12px;
    flex: 0 0 40%;
    margin-right: 12px;
    line-height: 1.3;
}

.info-value {
    color: #212529;
    font-weight: 500;
    text-align: right;
    font-size: 12px;
    flex: 1;
    line-height: 1.3;
    word-break: break-word;
}

.status-badge {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    padding: 4px 10px;
    border-radius: 15px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.amount-section {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 8px;
    padding: 18px;
    margin: 18px 0;
    border-left: 4px solid #667eea;
}

.amount-label {
    font-size: 14px;
    color: #495057;
    font-weight: 600;
    margin-bottom: 6px;
}

.amount-value {
    font-size: 24px;
    font-weight: 700;
    color: #667eea;
    margin: 0;
}

.customer-section {
    background: #fff;
    border: 1px solid #f0f0f0;
    border-radius: 8px;
    padding: 18px;
    margin: 18px 0;
}

.action-buttons {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    justify-content: center;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #f0f0f0;
}

.btn-modern {
    padding: 6px 14px;
    border-radius: 15px;
    border: none;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    font-size: 10px;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.btn-primary-modern {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}

.btn-success-modern {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
}

.btn-info-modern {
    background: linear-gradient(135deg, #17a2b8, #007bff);
    color: white;
}

.btn-default-modern {
    background: #f8f9fa;
    color: #495057;
    border: 2px solid #dee2e6;
}

.btn-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    color: white;
    text-decoration: none;
}

.footer-note {
    background: #f8f9fa;
    padding: 20px;
    text-align: center;
    color: #6c757d;
    font-style: italic;
    border-radius: 0 0 15px 15px;
    margin-top: 20px;
}

@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn-modern {
        width: 100%;
        justify-content: center;
    }
}

@media print {
    body {
        margin: 0;
        padding: 0;
        font-size: 12px;
        line-height: 1.3;
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
    }
    
    .modern-invoice {
        width: 100%;
        max-width: 210mm;
        min-height: auto;
        margin: 0;
        padding: 0;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
        border-radius: 8px;
        page-break-after: avoid;
    }
    
    .invoice-header {
        padding: 12px;
        break-inside: avoid;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        color: white !important;
    }
    
    .company-logo {
        background: rgba(255,255,255,0.2) !important;
        border: 2px solid rgba(255,255,255,0.3) !important;
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
    
    .status-badge {
        background: linear-gradient(135deg, #28a745, #20c997) !important;
        color: white !important;
    }
    
    .amount-section {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef) !important;
        border-left: 3px solid #667eea !important;
        padding: 15px;
        margin: 15px 0;
    }
    
    .amount-value {
        color: #667eea !important;
        font-size: 20px;
    }
    
    .info-section h4 {
        color: #667eea !important;
        border-bottom: 1px solid #f0f0f0 !important;
        font-size: 10px !important;
    }
    
    .customer-section {
        border: 1px solid #f0f0f0 !important;
        background: #fff !important;
        padding: 15px;
        margin: 15px 0;
    }
    
    .invoice-body {
        padding: 15px;
    }
    
    .info-grid {
        gap: 15px;
        margin-bottom: 15px;
    }
    
    .info-item {
        padding: 6px 0;
        min-height: 24px;
        border-bottom: 1px solid #f8f9fa !important;
    }
    
    .info-label {
        font-size: 10px;
        color: #495057 !important;
    }
    
    .info-value {
        font-size: 10px;
        color: #212529 !important;
    }
    
    .customer-section {
        padding: 15px;
        margin: 15px 0;
    }
    
    .amount-section {
        padding: 15px;
        margin: 15px 0;
    }
    
    .action-buttons {
        display: none;
    }
    
    .download-section {
        display: none;
    }
    
    .company-logo {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
    
    .invoice-title {
        font-size: 14px;
        color: white !important;
    }
    
    .amount-value {
        font-size: 20px;
    }
    
    .footer-note {
        padding: 12px;
        margin-top: 12px;
        background: #f8f9fa !important;
        color: #6c757d !important;
    }
}

.invoice-watermark {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) rotate(-45deg);
    font-size: 100px;
    color: rgba(0,0,0,0.03);
    font-weight: 900;
    z-index: 1;
    pointer-events: none;
}
</style>

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="modern-invoice">
            <!-- Invoice Header with Logo -->
            <div class="invoice-header">
                <div class="row">
                    <div class="col-md-8">
                        <div class="company-logo">
                            <?php if ($_smarty_tpl->tpl_vars['_c']->value['logo']) {?>
                                <img src="<?php echo $_smarty_tpl->tpl_vars['UPLOAD_PATH']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['_c']->value['logo'];?>
" alt="Logo" style="max-width: 40px; max-height: 40px; border-radius: 50%;">
                            <?php } else { ?>
                                <?php echo substr((string) $_smarty_tpl->tpl_vars['_c']->value['CompanyName'], (int) 0, (int) 1);?>

                            <?php }?>
                        </div>
                        <h1 class="invoice-title" style="font-size: 15px;"><?php echo $_smarty_tpl->tpl_vars['_c']->value['CompanyName'];?>
</h1>
                        <div style="opacity: 0.9; margin-top: 8px; font-size: 12px;">
                            <div><?php echo $_smarty_tpl->tpl_vars['_c']->value['address'];?>
</div>
                            <div><?php echo $_smarty_tpl->tpl_vars['_c']->value['phone'];?>
</div>
                        </div>
                    </div>
                    <div class="col-md-4 text-right">
                        <h2 class="invoice-title" style="font-size: 15px;">INVOICE</h2>
                        <div class="invoice-id" style="font-size: 12px;"><?php echo $_smarty_tpl->tpl_vars['in']->value['invoice'];?>
</div>
                        <div class="status-badge" style="margin-top: 10px;">PAID</div>
                    </div>
                </div>
            </div>

            <!-- Invoice Body -->
            <div class="invoice-body">
                <div class="invoice-watermark">PAID</div>
                
                <!-- Information Grid -->
                <div class="info-grid">
                    <div class="info-section">
                        <h4><i class="fa fa-calendar"></i> Transaction Details</h4>
                        <div class="info-item">
                            <span class="info-label">Date:</span>
                            <span class="info-value"><?php echo Lang::dateAndTimeFormat($_smarty_tpl->tpl_vars['in']->value['recharged_on'],$_smarty_tpl->tpl_vars['in']->value['recharged_time']);?>
</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Sales Person:</span>
                            <span class="info-value"><?php if ($_smarty_tpl->tpl_vars['in']->value['admin_id'] > 0) {
echo $_smarty_tpl->tpl_vars['_admin']->value['fullname'];
} else { ?>System<?php }?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Payment Method:</span>
                            <span class="info-value"><?php echo $_smarty_tpl->tpl_vars['in']->value['method'];?>
</span>
                        </div>
                    </div>

                    <div class="info-section">
                        <h4><i class="fa fa-cog"></i> Service Details</h4>
                        <div class="info-item">
                            <span class="info-label">Service Type:</span>
                            <span class="info-value"><?php echo $_smarty_tpl->tpl_vars['in']->value['type'];?>
</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Plan Name:</span>
                            <span class="info-value"><?php echo $_smarty_tpl->tpl_vars['in']->value['plan_name'];?>
</span>
                        </div>
                        <?php if ($_smarty_tpl->tpl_vars['in']->value['type'] != 'Balance') {?>
                        <div class="info-item">
                            <span class="info-label">Expires On:</span>
                            <span class="info-value"><?php echo Lang::dateAndTimeFormat($_smarty_tpl->tpl_vars['in']->value['expiration'],$_smarty_tpl->tpl_vars['in']->value['time']);?>
</span>
                        </div>
                        <?php }?>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="customer-section">
                    <h4 style="color: #667eea; margin-bottom: 15px; font-size: 12px;"><i class="fa fa-user"></i> Customer Information</h4>
                    <div class="info-grid">
                        <div>
                            <div class="info-item">
                                <span class="info-label">Full Name:</span>
                                <span class="info-value"><?php if ($_smarty_tpl->tpl_vars['customer']->value) {
echo $_smarty_tpl->tpl_vars['customer']->value['fullname'];
} else { ?>N/A<?php }?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Username:</span>
                                <span class="info-value"><?php echo $_smarty_tpl->tpl_vars['in']->value['username'];?>
</span>
                            </div>
                        </div>
                        <div>
                            <div class="info-item">
                                <span class="info-label">Password:</span>
                                <span class="info-value">**********</span>
                            </div>
                            <?php if ($_smarty_tpl->tpl_vars['customer']->value && $_smarty_tpl->tpl_vars['customer']->value['phonenumber']) {?>
                            <div class="info-item">
                                <span class="info-label">Phone:</span>
                                <span class="info-value"><?php echo $_smarty_tpl->tpl_vars['customer']->value['phonenumber'];?>
</span>
                            </div>
                            <?php }?>
                        </div>
                    </div>
                </div>

                <!-- Amount Section -->
                <div class="amount-section">
                    <div class="text-center">
                        <div class="amount-label">Total Amount</div>
                        <div class="amount-value"><?php echo Lang::moneyFormat($_smarty_tpl->tpl_vars['in']->value['price']);?>
</div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/list" class="btn-modern btn-default-modern">
                        <i class="fa fa-arrow-left"></i> <?php echo Lang::T('Finish');?>

                    </a>
                    
                    <a href="https://api.whatsapp.com/send/?text=<?php echo $_smarty_tpl->tpl_vars['whatsapp']->value;?>
" target="_blank" class="btn-modern btn-success-modern">
                        <i class="fa fa-whatsapp"></i> WhatsApp
                    </a>
                    
                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/view/<?php echo $_smarty_tpl->tpl_vars['in']->value['id'];?>
/send" class="btn-modern btn-info-modern">
                        <i class="fa fa-envelope"></i> <?php echo Lang::T("Resend");?>

                    </a>
                </div>

                <!-- Download Invoice Section -->
                <div class="download-section" style="margin-top: 18px; padding: 15px; background: #f8f9fa; border-radius: 8px; border-left: 3px solid #667eea;">
                    <h5 style="color: #667eea; margin-bottom: 12px; font-weight: 600; font-size: 12px;">
                        <i class="fa fa-download"></i> Download Invoice
                    </h5>
                    <div class="action-buttons" style="margin-top: 0; padding-top: 0; border-top: none;">
                        <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/print/<?php echo $_smarty_tpl->tpl_vars['in']->value['id'];?>
" target="_blank" class="btn-modern btn-primary-modern">
                            <i class="fa fa-file-pdf-o"></i> Download PDF
                        </a>

                        <form method="post" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/print" target="_blank" style="display: inline;">
                            <textarea class="hidden" name="content"><?php echo $_smarty_tpl->tpl_vars['invoice']->value;?>
</textarea>
                            <input type="hidden" name="id" value="<?php echo $_smarty_tpl->tpl_vars['in']->value['id'];?>
">
                            <button type="submit" class="btn-modern btn-info-modern">
                                <i class="fa fa-file-text-o"></i> Download Text
                            </button>
                        </form>

                        <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/print/<?php echo $_smarty_tpl->tpl_vars['in']->value['id'];?>
?format=html" target="_blank" class="btn-modern btn-success-modern">
                            <i class="fa fa-file-code-o"></i> Download HTML
                        </a>

                        <a href="nux://print?text=<?php echo rawurlencode((string)$_smarty_tpl->tpl_vars['invoice']->value);?>
" class="btn-modern btn-default-modern hidden-md hidden-lg">
                            <i class="fa fa-mobile"></i> NuxPrint
                        </a>
                    </div>
                </div>
            </div>

            <!-- Footer Note -->
            <?php if ($_smarty_tpl->tpl_vars['_c']->value['note']) {?>
            <div class="footer-note">
                <i class="fa fa-info-circle"></i> <?php echo $_smarty_tpl->tpl_vars['_c']->value['note'];?>

            </div>
            <?php }?>
        </div>
    </div>
</div>

<?php }?>

<?php $_smarty_tpl->_subTemplateRender("file:sections/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
