<?php
/**
 * Generic payment-status JSON endpoint used by the payment progress page.
 * Call:  ?_route=plugin/payment_status/{transactionId}
 * Returns the current status of a tbl_payment_gateway record.
 */
function payment_status()
{
    global $routes;
    header('Content-Type: application/json');
    $id = isset($routes['2']) ? intval($routes['2']) : 0;
    if ($id <= 0) {
        echo json_encode(['status' => 0, 'paid' => false]);
        exit;
    }
    $trx = ORM::for_table('tbl_payment_gateway')->find_one($id);
    if (!$trx) {
        echo json_encode(['status' => 0, 'paid' => false]);
        exit;
    }
    echo json_encode([
        'status' => intval($trx['status']),
        'paid'   => (intval($trx['status']) == 2),
        'msg'    => $trx['pg_paid_response'],
    ]);
    exit;
}
