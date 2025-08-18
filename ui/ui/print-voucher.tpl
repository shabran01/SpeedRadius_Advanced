<!DOCTYPE html>
<html>
<head>
    <title>{$_title}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="ui/ui/images/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary-color: #0ea5e9;
            --accent-color: #8b5cf6;
            --success-color: #059669;
            --warning-color: #d97706;
            --text-color: #1f2937;
            --text-light: #6b7280;
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: var(--text-color);
            line-height: 1.6;
            background: var(--bg-color);
            min-height: 100vh;
        }

        .container {
            max-width: 21cm;
            margin: 0.5rem auto;
            padding: 0 0.5rem;
        }

        .page-title {
            text-align: center;
            padding: 0.75rem 0;
            margin-bottom: 1rem;
            position: relative;
            overflow: hidden;
        }

        .page-title h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            z-index: 1;
        }

        .page-title h1 i {
            font-size: 1.25rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: pulse 2s infinite;
        }

        .title-accent {
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: linear-gradient(90deg, 
                transparent 0%, 
                var(--primary-color) 20%, 
                var(--secondary-color) 80%, 
                transparent 100%
            );
            border-radius: 3px;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }

        .controls-panel {
            background: var(--card-bg);
            border-radius: 0.5rem;
            padding: 0.75rem;
            margin-bottom: 1rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
        }

        @media (max-width: 768px) {
            .container {
                margin: 0.25rem auto;
                padding: 0 0.25rem;
            }
            
            .controls-panel {
                padding: 0.5rem;
                border-radius: 0.375rem;
            }
        }

        .controls-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 0.5rem;
            align-items: end;
        }

        .control-group {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .control-group label {
            font-weight: 500;
            color: var(--text-light);
            font-size: 0.75rem;
            margin-bottom: 0;
        }

        input[type="text"], select {
            padding: 0.375rem 0.5rem;
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            font-size: 0.8125rem;
            width: 100%;
            transition: all 0.2s ease;
            color: var(--text-color);
            background: var(--bg-color);
            box-shadow: var(--shadow-sm);
            height: 2rem;
        }

        input[type="text"]:hover, select:hover {
            border-color: var(--secondary-color);
        }

        input[type="text"]:focus, select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .btn {
            padding: 0.375rem 0.75rem;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.8125rem;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.375rem;
            box-shadow: var(--shadow-sm);
            height: 2rem;
            white-space: nowrap;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-secondary {
            background: var(--secondary-color);
            color: white;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        @media (max-width: 1024px) {
            .controls-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 0.5rem;
            }
        }

        @media (max-width: 768px) {
            .controls-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.5rem;
            }
        }

        @media (max-width: 480px) {
            .controls-grid {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }
            
            .btn {
                width: 100%;
            }
        }

        .print-info {
            text-align: center;
            margin: 0.5rem 0;
            padding: 0.75rem;
            background: var(--bg-color);
            border-radius: 0.375rem;
            font-size: 0.75rem;
        }

        .print-info p {
            color: var(--text-light);
            margin: 0.25rem 0;
            font-size: 0.75rem;
            display: inline-block;
            margin-right: 1rem;
        }

        #print-button {
            margin: 0.5rem 0;
        }

        .voucher-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            padding: 1rem;
            max-width: 100%;
            margin: 0 auto;
        }

        .voucher-card {
            background: var(--card-bg);
            border: 2px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1rem;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: transform 0.2s ease;
            min-width: 0;
        }

        .voucher-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .voucher-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        }

        .wifi-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-color);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .wifi-title i {
            color: var(--primary-color);
        }

        .voucher-details {
            display: grid;
            gap: 0.75rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px dashed var(--border-color);
        }

        .detail-label {
            color: var(--text-light);
            font-size: 0.875rem;
        }

        .detail-value {
            font-weight: 600;
            color: var(--text-color);
        }

        .qr-code {
            margin-top: 1rem;
            text-align: center;
        }

        .qr-code img {
            max-width: 100%;
            height: auto;
        }

        .page-break {
            height: 4px;
            background: linear-gradient(to right, var(--accent-color), var(--secondary-color));
            margin: 2rem 0;
            border-radius: 2px;
        }

        page[size="A4"] {
            background: white;
            width: 21cm;
            height: 29.7cm;
            display: block;
            margin: 0 auto;
            margin-bottom: 1cm;
            padding: 1.5cm;
            box-shadow: var(--shadow-lg);
            border-radius: 0.5rem;
        }

        @media screen and (max-width: 1200px) {
            .voucher-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media screen and (max-width: 768px) {
            .voucher-grid {
                grid-template-columns: 1fr;
                padding: 0.5rem;
                gap: 0.75rem;
            }

            .voucher-card {
                padding: 0.75rem;
            }
        }

        @media print {
            .voucher-grid {
                grid-template-columns: repeat(3, 1fr) !important;
                gap: 0.5cm;
                padding: 0;
            }

            .voucher-card {
                page-break-inside: avoid;
                break-inside: avoid;
                padding: 0.5cm;
            }
        }

        .loading {
            position: relative;
            pointer-events: none;
            opacity: 0.7;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 1.5rem;
            height: 1.5rem;
            margin: -0.75rem 0 0 -0.75rem;
            border: 3px solid var(--border-color);
            border-top-color: var(--primary-color);
            border-radius: 50%;
            animation: spinner 0.6s linear infinite;
        }

        @keyframes spinner {
            to {
                transform: rotate(360deg);
            }
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media print {
            body {
                background: white;
                margin: 0;
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            page[size="A4"] {
                margin: 0;
                padding: 0.5cm;
                box-shadow: none;
                border-radius: 0;
            }

            .controls-panel,
            .no-print,
            .no-print * {
                display: none !important;
            }

            .voucher-grid {
                gap: 0.5cm;
            }

            .voucher-card {
                break-inside: avoid;
                page-break-inside: avoid;
                box-shadow: none !important;
                border: 1px solid var(--border-color) !important;
            }

            .page-break {
                display: block;
                page-break-before: always;
                height: 0;
                margin: 0;
                border: 0;
            }

            .print-info {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="page-title no-print">
            <h1><i class="fas fa-ticket-alt"></i> Print Vouchers</h1>
            <div class="title-accent"></div>
        </div>
        <form method="post" action="{$_url}plan/print-voucher/" class="controls-panel no-print">
            <div class="controls-grid">
                <div class="control-group">
                    <label>From ID</label>
                    <input type="text" name="from_id" value="{$from_id}" placeholder="ID">
                </div>
                <div class="control-group">
                    <label>Limit</label>
                    <input type="text" name="limit" value="{$limit}" placeholder="Count">
                </div>
                <div class="control-group">
                    <label>Per Line</label>
                    <input type="text" name="vpl" value="{$vpl}" placeholder="Per row">
                </div>
                <div class="control-group">
                    <label>Break After</label>
                    <input type="text" name="pagebreak" value="{$pagebreak}" placeholder="Per page">
                </div>
                <div class="control-group">
                    <label>Plan</label>
                    <select id="plan_id" name="planid">
                        <option value="0">All Plans</option>
                        {foreach $plans as $plan}
                            <option value="{$plan['id']}" {if $plan['id']==$planid}selected{/if}>{$plan['name_plan']}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="control-group">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-sync-alt"></i> Generate</button>
                </div>
            </div>
        </form>

        <div class="print-info no-print">
            <button type="button" id="print-button" onclick="window.print()" class="btn btn-secondary">
                <i class="fas fa-print"></i> {Lang::T('Print')}
            </button>
            <p>{Lang::T('Print side by side, it will easy to cut')}</p>
            <p>Showing {$v|@count} of {$vc} vouchers</p>
            <p>From ID {$v[0]['id']}, limit {$limit}</p>
        </div>

        <page size="A4">
            <div id="printable">
                <div class="voucher-grid">
                    {$n = 1}
                    {foreach $voucher as $vs}
                        {$jml = $jml + 1}
                        {if $n == 1}
                            <div class="voucher-row">
                        {/if}
                        <div class="voucher-card">{$vs}</div>
                        {if $n == $vpl}
                            </div>
                            {$n = 1}
                        {else}
                            {$n = $n + 1}
                        {/if}

                        {if $jml == $pagebreak}
                            {$jml = 0}
                            <div class="page-break">
                                <div class="no-print print-break-indicator">Page Break</div>
                            </div>
                        {/if}
                    {/foreach}
                </div>
            </div>
        </page>
    </div>

    <script src="ui/ui/scripts/jquery.min.js"></script>
    {if isset($xfooter)}
        {$xfooter}
    {/if}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const printBtn = document.querySelector('.btn-secondary');
            const form = document.querySelector('form');
            const inputs = form.querySelectorAll('input[type="text"]');
            const submitBtn = form.querySelector('button[type="submit"]');
            
            // Add icons to buttons
            printBtn.innerHTML = '<i class="fas fa-print"></i> {Lang::T("Print Vouchers")}';
            submitBtn.innerHTML = '<i class="fas fa-sync-alt"></i> {Lang::T("Generate Vouchers")}';

            // Print button handler with loading state
            printBtn.addEventListener('click', function(e) {
                const btn = e.target.closest('button');
                btn.classList.add('loading');
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {Lang::T("Preparing")}...';
                
                setTimeout(() => {
                    window.print();
                    setTimeout(() => {
                        btn.classList.remove('loading');
                        btn.innerHTML = '<i class="fas fa-print"></i> {Lang::T("Print Vouchers")}';
                    }, 1000);
                }, 100);
            });

            // Enhanced form validation with visual feedback
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    validateInput(this);
                });

                input.addEventListener('blur', function() {
                    validateInput(this, true);
                });
            });

            form.addEventListener('submit', function(e) {
                let valid = true;
                
                inputs.forEach(input => {
                    if (!validateInput(input, true)) {
                        valid = false;
                    }
                });

                if (!valid) {
                    e.preventDefault();
                    showError('{Lang::T("Please enter valid positive numbers for all numeric fields")}');
                }
            });

            function validateInput(input, showError = false) {
                const value = parseInt(input.value);
                const isValid = !isNaN(value) && value >= 0;
                
                if (isValid) {
                    input.style.borderColor = 'var(--success-color)';
                    input.style.backgroundColor = 'rgba(5, 150, 105, 0.1)';
                } else if (showError) {
                    input.style.borderColor = 'var(--warning-color)';
                    input.style.backgroundColor = 'rgba(217, 119, 6, 0.1)';
                }
                
                return isValid;
            }

            function showError(message) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message fade-in';
                errorDiv.style.cssText = `
                    background: var(--warning-color);
                    color: white;
                    padding: 1rem;
                    border-radius: 0.5rem;
                    margin: 1rem 0;
                    text-align: center;
                    font-weight: 500;
                `;
                errorDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + message;
                
                form.insertBefore(errorDiv, form.firstChild);
                
                setTimeout(() => {
                    errorDiv.remove();
                }, 5000);
            }

            // Responsive voucher grid
            function adjustVoucherGrid() {
                var grid = document.querySelector('.voucher-grid');
                if (grid) {
                    var width = window.innerWidth;
                    var columns = width < 768 ? 1 : (width < 1024 ? 2 : 3);
                    grid.style.gridTemplateColumns = 'repeat(' + columns + ', 1fr)';
                }
            }

            // Initialize grid and add resize listener
            window.addEventListener('resize', adjustVoucherGrid);
            adjustVoucherGrid();

            // Add animation to vouchers
            document.querySelectorAll('.voucher-card').forEach((card, index) => {
                card.style.animationDelay = (index * 0.1) + 's';
                card.classList.add('fade-in');
            });
        });
        });
    </script>

</body>
</html>