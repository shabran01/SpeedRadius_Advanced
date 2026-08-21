<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$_title}</title>
    <link rel="shortcut icon" type="image/x-icon" href="ui/ui/images/favicon.ico">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .invoice {
            width: 100%;
            max-width: 600px;
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #667eea;
        }

        .company-logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            color: white;
            margin: 0 auto 15px auto;
        }

        .header h1 {
            margin: 0 0 10px 0;
            font-size: 24px;
            font-weight: 600;
            color: #667eea;
        }

        .header p {
            margin: 0;
            color: #6c757d;
            font-size: 14px;
        }

        .invoice-info-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 8px;
        }

        .invoice-details h2 {
            margin: 0;
            color: #495057;
            font-size: 18px;
        }

        .invoice-id {
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
            margin: 5px 0;
        }

        .status-badge {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .details-section h3 {
            color: #667eea;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 5px;
        }

        .invoice-info {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .invoice-info th,
        .invoice-info td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }

        .invoice-info th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            font-size: 14px;
        }

        .invoice-info td {
            color: #212529;
            font-weight: 500;
        }

        .total-section {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            margin: 30px 0;
        }

        .total-label {
            font-size: 16px;
            margin-bottom: 10px;
            opacity: 0.9;
        }

        .total-amount {
            font-size: 32px;
            font-weight: 700;
            margin: 0;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
            color: #6c757d;
            font-style: italic;
        }

        @media print {
            body {
                margin: 0;
                padding: 10px;
                font-size: 12px;
                background: white;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }

            .invoice {
                width: 100%;
                max-width: none;
                padding: 20px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
                border: 1px solid #e0e0e0;
                border-radius: 8px;
            }

            .btn {
                display: none;
            }

            .header {
                background: linear-gradient(135deg, #667eea, #764ba2) !important;
                color: white !important;
                border-bottom: none;
                padding: 20px;
                border-radius: 8px 8px 0 0;
            }

            .company-logo {
                background: linear-gradient(135deg, #667eea, #764ba2) !important;
                color: white !important;
            }

            .invoice-info-header {
                background: linear-gradient(135deg, #f8f9fa, #e9ecef) !important;
                border: 1px solid #dee2e6;
            }

            .status-badge {
                background: linear-gradient(135deg, #28a745, #20c997) !important;
                color: white !important;
            }

            .total-section {
                background: linear-gradient(135deg, #667eea, #764ba2) !important;
                color: white !important;
            }

            .details-section h3 {
                color: #667eea !important;
                border-bottom: 2px solid #f0f0f0 !important;
            }

            .invoice-info th {
                background: #f8f9fa !important;
                color: #495057 !important;
            }
        }

        @media (max-width: 600px) {
            .details-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .invoice-info-header {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
        }
    </style>
    <script type="text/javascript">
        function printpage() {
            window.print();
        }
    </script>
</head>

<body {if !$nuxprint} onload="printpage()" {/if}>
    <div class="container">
        <div class="invoice">
            {if $content}
                <div style="background: white; padding: 20px; border-radius: 8px;">
                    <pre style="border: none; background: none; font-family: 'Courier New', monospace; white-space: pre-wrap; margin: 0;">{$content}</pre>
                </div>
            {else}
                <!-- Header with Logo -->
                <div class="header">
                    <div class="company-logo">
                        {if $_c.logo}
                            <img src="{$UPLOAD_PATH}/{$_c.logo}" alt="Logo" style="max-width: 50px; max-height: 50px; border-radius: 50%;">
                        {else}
                            {$_c.CompanyName|substr:0:1}
                        {/if}
                    </div>
                    <h1>{$_c.CompanyName}</h1>
                    <p>{$_c.address} | {$_c.phone}</p>
                </div>

                <!-- Invoice Header Info -->
                <div class="invoice-info-header">
                    <div class="invoice-details">
                        <h2>Invoice Details</h2>
                        <div class="invoice-id">{$in.invoice}</div>
                        <div style="color: #6c757d; font-size: 14px;">{Lang::dateAndTimeFormat($in.recharged_on, $in.recharged_time)}</div>
                    </div>
                    <div class="status-badge">PAID</div>
                </div>

                <!-- Details Grid -->
                <div class="details-grid">
                    <div class="details-section">
                        <h3>Transaction Information</h3>
                        <table style="width: 100%; border: none;">
                            <tr>
                                <td style="border: none; padding: 8px 0; font-weight: 600; color: #495057;">Sales Person:</td>
                                <td style="border: none; padding: 8px 0;">{$_admin.fullname}</td>
                            </tr>
                            <tr>
                                <td style="border: none; padding: 8px 0; font-weight: 600; color: #495057;">Payment Method:</td>
                                <td style="border: none; padding: 8px 0;">{$in.method}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="details-section">
                        <h3>Customer Information</h3>
                        {assign var="customer" value=ORM::for_table('tbl_customers')->where('username', $in.username)->find_one()}
                        <table style="width: 100%; border: none;">
                            <tr>
                                <td style="border: none; padding: 8px 0; font-weight: 600; color: #495057;">Full Name:</td>
                                <td style="border: none; padding: 8px 0;">{if $customer}{$customer.fullname}{else}N/A{/if}</td>
                            </tr>
                            <tr>
                                <td style="border: none; padding: 8px 0; font-weight: 600; color: #495057;">Username:</td>
                                <td style="border: none; padding: 8px 0;">{$in.username}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Service Details Table -->
                <table class="invoice-info">
                    <thead>
                        <tr>
                            <th>Service Details</th>
                            <th>Information</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Service Type</td>
                            <td>{$in.type}</td>
                        </tr>
                        <tr>
                            <td>Plan Name</td>
                            <td>{$in.plan_name}</td>
                        </tr>
                        <tr>
                            <td>Password</td>
                            <td>**********</td>
                        </tr>
                        {if $in.type != 'Balance'}
                            <tr>
                                <td>Created On</td>
                                <td>{Lang::dateAndTimeFormat($in.recharged_on, $in.recharged_time)}</td>
                            </tr>
                            <tr>
                                <td>Expires On</td>
                                <td>{Lang::dateAndTimeFormat($in.expiration, $in.time)}</td>
                            </tr>
                        {/if}
                    </tbody>
                </table>

                <!-- Total Amount Section -->
                <div class="total-section">
                    <div class="total-label">Total Amount</div>
                    <div class="total-amount">{Lang::moneyFormat($in.price)}</div>
                </div>

                <!-- Footer -->
                <div class="footer">
                    {if $_c.note}
                        <p>{$_c.note}</p>
                    {/if}
                    
                    {if $nuxprint}
                        <a href="{$nuxprint}" class="btn btn-success" style="display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin-top: 15px;">
                            <i class="glyphicon glyphicon-print"></i> Nux Print
                        </a>
                    {/if}
                </div>
            {/if}
        </div>
    </div>

    <script src="ui/ui/scripts/jquery.min.js"></script>
    <script src="ui/ui/scripts/bootstrap.min.js"></script>
    {if isset($xfooter)} {$xfooter} {/if}
</body>

</html>