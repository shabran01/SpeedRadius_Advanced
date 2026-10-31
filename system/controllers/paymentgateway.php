<?php

/**
 *  PHP Mikrotik Billing (https://github.com/hotspotbilling/phpnuxbill/)
 *  by https://t.me/ibnux
 **/

_admin();
$ui->assign('_system_menu', 'paymentgateway');

$action = alphanumeric($routes[1]);
$ui->assign('_admin', $admin);
switch ($action) {
    case 'delete':
        $pg = alphanumeric($routes[2]);
        $target = $PAYMENTGATEWAY_PATH . DIRECTORY_SEPARATOR . $pg . '.php';
        if ($pg !== '' && is_file($target)) {
            unlink($target);
        }
        r2(U . 'paymentgateway', 's', Lang::T('Payment Gateway Deleted'));

    case 'audit':
        $pg = alphanumeric($routes[2]);
        $q = alphanumeric(_req('q'), '-._ ');
        $append_url = '';
        $query = ORM::for_table('tbl_payment_gateway')->order_by_desc("id");
        $query->selects('id', 'username', 'gateway', 'gateway_trx_id', 'plan_id', 'plan_name', 'routers_id', 'routers', 'price', 'pg_url_payment', 'payment_method', 'payment_channel', 'expired_date', 'created_date', 'paid_date', 'trx_invoice', 'status');
        if (!empty($pg)) {
            $query->where('gateway', $pg);
        }
        if (!empty($q)) {
            $query->whereRaw("(gateway_trx_id LIKE '%$q%' OR username LIKE '%$q%' OR routers LIKE '%$q%' OR plan_name LIKE '%$q%')");
            $append_url = '&q=' . urlencode($q);
        }
        $pgs = Paginator::findMany($query, [], 50, $append_url);

        $ui->assign('_title', 'Payment Gateway Audit');
        $ui->assign('pgs', $pgs);
        $ui->assign('pg', $pg);
        $ui->assign('q', $q);
        $ui->display('paymentgateway-audit.tpl');
        break;
    case 'auditview':
        $pg = (int) $routes[2];
        $d = ORM::for_table('tbl_payment_gateway')->find_one($pg);
        if (!$d) {
            r2(U . 'paymentgateway', 'e', Lang::T('Payment Gateway record not found'));
        }
        $d['pg_request'] = (!empty($d['pg_request']))? Text::jsonArray21Array(json_decode($d['pg_request'], true)) : [];
        $d['pg_paid_response'] = (!empty($d['pg_paid_response']))? Text::jsonArray21Array(json_decode($d['pg_paid_response'], true)) : [];
        $ui->assign('_title', 'Payment Gateway Audit View');
        $ui->assign('pg', $d);
        $ui->display('paymentgateway-audit-view.tpl');
        break;
    default:
        if (_post('save') == 'actives') {
            if (!Csrf::check(_post('csrf_token'))) {
                r2(U . 'paymentgateway', 'e', Lang::T('Invalid or Expired CSRF Token') . '.');
            }
            $pgs = '';
            if (isset($_POST['pgs']) && is_array($_POST['pgs'])) {
                $pgs = implode(',', $_POST['pgs']);
            }
            $d = ORM::for_table('tbl_appconfig')->where('setting', 'payment_gateway')->find_one();
            if ($d) {
                $d->value = $pgs;
                $d->save();
            } else {
                $d = ORM::for_table('tbl_appconfig')->create();
                $d->setting = 'payment_gateway';
                $d->value = $pgs;
                $d->save();
            }
            r2(U . 'paymentgateway', 's', Lang::T('Payment Gateway saved successfully'));
        }

        if (file_exists($PAYMENTGATEWAY_PATH . DIRECTORY_SEPARATOR . $action . '.php')) {
            include $PAYMENTGATEWAY_PATH . DIRECTORY_SEPARATOR . $action . '.php';
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (function_exists($action . '_save_config')) {
                    call_user_func($action . '_save_config');
                } else {
                    $ui->display('a404.tpl');
                }
            } else {
                if (function_exists($action . '_show_config')) {
                    call_user_func($action . '_show_config');
                } else {
                    $ui->display('a404.tpl');
                }
            }
        } else {
            if (!empty($action)) {
                r2(U . 'paymentgateway', 'w', Lang::T('Payment Gateway Not Found'));
            } else {
                $pgs = [];
                $files = scandir($PAYMENTGATEWAY_PATH);
                foreach ($files as $file) {
                    if (pathinfo($file, PATHINFO_EXTENSION) == 'php') {
                        $pgs[] = str_replace('.php', '', $file);
                    }
                }
                $ui->assign('_title', 'Payment Gateway Settings');
                $ui->assign('pgs', $pgs);
                $ui->assign('actives', explode(',', $config['payment_gateway'] ?? ''));
                $ui->assign('csrf_token', Csrf::generateAndStoreToken());
                $ui->display('paymentgateway.tpl');
            }
        }
}


function deleteFile($path, $name)
{
    $files = scandir($path);
    foreach ($files as $file) {
        if (is_file($path . $file) && strpos($file, $name) !== false) {
            unlink($path . $file);
        } else if (is_dir($path . $file) && !in_array($file, ['.', '..'])) {
            deleteFile($path . $file . DIRECTORY_SEPARATOR, $name);
        }
    }
}
