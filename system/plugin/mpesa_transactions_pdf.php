<?php

// Fix the autoload path - this should point to the vendor directory
$autoload_paths = [
    __DIR__ . '/../../vendor/autoload.php',
    __DIR__ . '/../vendor/autoload.php',
    dirname(__DIR__) . '/vendor/autoload.php'
];

$autoload_found = false;
foreach ($autoload_paths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $autoload_found = true;
        break;
    }
}

if (!$autoload_found) {
    die("Error: Could not find Composer autoload.php file. Please ensure mPDF is installed via Composer.");
}

use Mpdf\Mpdf;

function generate_mpesa_transactions_pdf($transactions) {
    try {
        // Validate input
        if (!is_array($transactions)) {
            throw new Exception("Invalid transaction data provided");
        }
        
        if (empty($transactions)) {
            // If no transactions, create a simple message PDF
            $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
            $html = '<h1>No Transactions Found</h1><p>No M-Pesa transactions were found for the selected date range.</p>';
            $mpdf->WriteHTML($html);
            $mpdf->Output('No_Transactions_Found.pdf', 'D');
            return;
        }
        
        // Use minimal configuration to avoid issues
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 20,
            'margin_bottom' => 20
        ]);

    // Clean, Simple Header
    $header = '
    <div style="border-bottom: 3px solid #3b82f6; padding-bottom: 20px; margin-bottom: 30px;">
        <table width="100%" cellpadding="0" cellspacing="0" style="border: none;">
            <tr>
                <td style="border: none;">
                    <h1 style="font-size: 28px; font-weight: bold; margin: 0; color: #1f2937;">
                        M-Pesa Transactions Report
                    </h1>
                    <p style="font-size: 16px; margin: 5px 0 0 0; color: #6b7280;">
                        Payment Transaction Summary
                    </p>
                </td>
                <td style="text-align: right; border: none;">
                    <div style="text-align: right;">
                        <p style="font-size: 14px; margin: 0; color: #374151; font-weight: bold;">
                            Report Date: ' . date('F d, Y') . '
                        </p>
                        <p style="font-size: 14px; margin: 3px 0 0 0; color: #6b7280;">
                            Generated at: ' . date('H:i A') . '
                        </p>
                        <p style="font-size: 14px; margin: 8px 0 0 0; color: #3b82f6; font-weight: bold;">
                            SpeedRadius ISP
                        </p>
                    </div>
                </td>
            </tr>
        </table>
    </div>';

    // Clean Content Styles
    $content = '
    <style>
        * {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        
        body {
            background: #ffffff;
            color: #374151;
            line-height: 1.5;
            font-size: 14px;
        }
        
        .summary-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
        }
        
        .summary-item {
            display: table-cell;
            text-align: center;
            padding: 0 15px;
            border-right: 1px solid #e5e7eb;
        }
        
        .summary-item:last-child {
            border-right: none;
        }
        
        .summary-value {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }
        
        .summary-label {
            font-size: 12px;
            color: #6b7280;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .table-container {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        
        th {
            background: #f3f4f6;
            color: #374151;
            font-weight: bold;
            padding: 12px 10px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        td {
            padding: 12px 10px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: top;
        }
        
        tr:nth-child(even) {
            background: #f9fafb;
        }
        
        .customer-name {
            font-weight: bold;
            color: #1f2937;
            font-size: 14px;
        }
        
        .phone-number {
            color: #6b7280;
            font-size: 12px;
            margin-top: 2px;
        }
        
        .amount {
            color: #059669;
            font-weight: bold;
            font-size: 14px;
        }
        
        .transaction-id {
            color: #3b82f6;
            font-weight: bold;
            font-size: 11px;
            background: #f3f4f6;
            padding: 3px 6px;
            border-radius: 4px;
        }
        
        .account-ref {
            color: #7c3aed;
            font-weight: bold;
            font-size: 13px;
        }
        
        .date-time {
            color: #374151;
            font-size: 12px;
        }
        
        .time-detail {
            color: #6b7280;
            font-size: 11px;
            margin-top: 2px;
        }
        
        .footer {
            margin-top: 30px;
            padding: 20px;
            background: #f9fafb;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #e5e7eb;
        }
        
        .footer-title {
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .footer-text {
            color: #6b7280;
            font-size: 13px;
            line-height: 1.6;
        }
    </style>';

    // Calculate summary statistics
    $total_amount = 0;
    $total_transactions = count($transactions);
    
    foreach ($transactions as $ts) {
        $total_amount += $ts['TransAmount'];
    }

    // Add clean summary section and table header only
    $content .= '
    <div class="summary-box">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value">' . number_format($total_transactions) . '</div>
                <div class="summary-label">Total Transactions</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">KES ' . number_format($total_amount, 0) . '</div>
                <div class="summary-label">Total Amount</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">KES ' . number_format($total_amount / max($total_transactions, 1), 0) . '</div>
                <div class="summary-label">Average Transaction</div>
            </div>
        </div>
    </div>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 25%;">Customer</th>
                    <th style="width: 15%;">Amount</th>
                    <th style="width: 15%;">Account</th>
                    <th style="width: 12%;">Balance</th>
                    <th style="width: 13%;">Transaction ID</th>
                    <th style="width: 15%;">Date & Time</th>
                </tr>
            </thead>
            <tbody>';

    // Don't add table rows here - they'll be added in batches

    // Write content to PDF - use different strategies based on data size
    if (count($transactions) <= 100) {
        // For small datasets, use simple approach
        $simple_content = $header . $content;
        
        // Add all transactions at once for small datasets
        foreach ($transactions as $key => $ts) {
            $date_time = date('M d, Y', strtotime($ts['TransTime']));
            $time_only = date('H:i', strtotime($ts['TransTime']));
            
            $simple_content .= '
            <tr>
                <td style="text-align: center; font-weight: bold; color: #6b7280;">' . ($key + 1) . '</td>
                <td>
                    <div class="customer-name">' . htmlspecialchars($ts['FirstName']) . '</div>
                    <div class="phone-number">' . (isset($ts['MSISDN']) && $ts['MSISDN'] ? htmlspecialchars($ts['MSISDN']) : 'No Phone') . '</div>
                </td>
                <td class="amount">KES ' . number_format($ts['TransAmount'], 2) . '</td>
                <td class="account-ref">' . htmlspecialchars($ts['BillRefNumber']) . '</td>
                <td style="color: #6b7280; font-size: 12px;">KES ' . number_format($ts['OrgAccountBalance'], 2) . '</td>
                <td class="transaction-id">' . htmlspecialchars($ts['TransID']) . '</td>
                <td>
                    <div class="date-time">' . $date_time . '</div>
                    <div class="time-detail">' . $time_only . '</div>
                </td>
            </tr>';
        }
        
        $simple_content .= '</tbody></table></div>
        <div class="footer">
            <div class="footer-title">SpeedRadius ISP Management System</div>
            <div class="footer-text">
                This report contains <strong>' . number_format($total_transactions) . '</strong> M-Pesa transactions with a total value of <strong>KES ' . number_format($total_amount, 2) . '</strong>
            </div>
            <div class="footer-text" style="margin-top: 10px;">
                Generated on ' . date('l, F j, Y \a\t g:i A') . ' | All amounts are in Kenya Shillings (KES)
            </div>
        </div>';
        
        $mpdf->WriteHTML($simple_content);
        
    } else {
        // For large datasets, use batch processing
        $mpdf->WriteHTML($header);
        $mpdf->WriteHTML($content);
        
        // Add table content in batches to avoid memory issues
        $batch_size = 25; // Smaller batch size for large datasets
        $total_batches = ceil(count($transactions) / $batch_size);
        
        for ($batch = 0; $batch < $total_batches; $batch++) {
            $start_index = $batch * $batch_size;
            $batch_transactions = array_slice($transactions, $start_index, $batch_size);
            
            $batch_content = '';
            foreach ($batch_transactions as $key => $ts) {
                $actual_key = $start_index + $key;
                $date_time = date('M d, Y', strtotime($ts['TransTime']));
                $time_only = date('H:i', strtotime($ts['TransTime']));
                
                $batch_content .= '
                <tr>
                    <td style="text-align: center; font-weight: bold; color: #6b7280;">' . ($actual_key + 1) . '</td>
                    <td>
                        <div class="customer-name">' . htmlspecialchars($ts['FirstName']) . '</div>
                        <div class="phone-number">' . (isset($ts['MSISDN']) && $ts['MSISDN'] ? htmlspecialchars($ts['MSISDN']) : 'No Phone') . '</div>
                    </td>
                    <td class="amount">KES ' . number_format($ts['TransAmount'], 2) . '</td>
                    <td class="account-ref">' . htmlspecialchars($ts['BillRefNumber']) . '</td>
                    <td style="color: #6b7280; font-size: 12px;">KES ' . number_format($ts['OrgAccountBalance'], 2) . '</td>
                    <td class="transaction-id">' . htmlspecialchars($ts['TransID']) . '</td>
                    <td>
                        <div class="date-time">' . $date_time . '</div>
                        <div class="time-detail">' . $time_only . '</div>
                    </td>
                </tr>';
            }
            
            $mpdf->WriteHTML($batch_content);
        }
        
        // Close the table and add footer
        $table_close = '</tbody></table></div>';
        $footer_html = '
        <div class="footer">
            <div class="footer-title">SpeedRadius ISP Management System</div>
            <div class="footer-text">
                This report contains <strong>' . number_format($total_transactions) . '</strong> M-Pesa transactions with a total value of <strong>KES ' . number_format($total_amount, 2) . '</strong>
            </div>
            <div class="footer-text" style="margin-top: 10px;">
                Generated on ' . date('l, F j, Y \a\t g:i A') . ' | All amounts are in Kenya Shillings (KES)
            </div>
        </div>';
        
        $mpdf->WriteHTML($table_close . $footer_html);
    }

    // Set filename with clean format
    $filename = 'Mpesa_Transactions_Report_' . date('Y-m-d_H-i-s') . '.pdf';

    // Output PDF for download
    $mpdf->Output($filename, 'D');
    
    } catch (Exception $e) {
        // If PDF generation fails, show detailed error for debugging
        error_log("PDF Generation Error: " . $e->getMessage());
        error_log("PDF Generation Stack Trace: " . $e->getTraceAsString());
        
        header('Content-Type: text/html');
        echo "<h3>Error generating PDF</h3>";
        echo "<p>There was an error generating the PDF report.</p>";
        echo "<p><strong>Error Details:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p>Please check the server logs for more details or contact the administrator.</p>";
        echo "<p><a href='javascript:history.back()'>Go Back</a></p>";
        exit;
    }
}
