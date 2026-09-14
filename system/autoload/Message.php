<?php

/**
 *  PHP Mikrotik Billing (https://github.com/hotspotbilling/phpnuxbill/)
 *  by https://t.me/ibnux
 **/

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use PEAR2\Net\RouterOS;

require $root_path . 'system/autoload/mail/Exception.php';
require $root_path . 'system/autoload/mail/PHPMailer.php';
require $root_path . 'system/autoload/mail/SMTP.php';

class Message
{

    public static function sendTelegram($txt)
    {
        global $config;
        run_hook('send_telegram', [$txt]); #HOOK
        if (!empty($config['telegram_bot']) && !empty($config['telegram_target_id'])) {
            return Http::getData('https://api.telegram.org/bot' . $config['telegram_bot'] . '/sendMessage?chat_id=' . $config['telegram_target_id'] . '&text=' . urlencode($txt));
        }
    }


    public static function sendSMS($phone, $txt)
    {
        global $config;
        if (empty($txt)) {
            return false;
        }
        $hookResult = run_hook('send_sms', [$phone, $txt]); #HOOK
        // If a plugin gateway handled the SMS, return its result
        if (!empty($config['active_sms_gateway']) && $hookResult !== false) {
            return true;
        }
        if (!empty($config['sms_url'])) {
            if (strlen($config['sms_url']) > 4 && substr($config['sms_url'], 0, 4) != "http") {
                if (strlen($txt) > 160) {
                    $txts = str_split($txt, 160);
                    try {
                        foreach ($txts as $txt) {
                            self::MikrotikSendSMS($config['sms_url'], $phone, $txt);
                        }
                        return true;
                    } catch (Exception $e) {
                        // ignore, add to logs
                        _log("Failed to send SMS using Mikrotik.\n" . $e->getMessage(), 'SMS', 0);
                        return false;
                    }
                } else {
                    try {
                        self::MikrotikSendSMS($config['sms_url'], $phone, $txt);
                        return true;
                    } catch (Exception $e) {
                        // ignore, add to logs
                        _log("Failed to send SMS using Mikrotik.\n" . $e->getMessage(), 'SMS', 0);
                        return false;
                    }
                }
            } else {
                $smsurl = str_replace('[number]', urlencode($phone), $config['sms_url']);
                $smsurl = str_replace('[text]', urlencode($txt), $smsurl);
                $result = Http::getData($smsurl);
                return !empty($result); // Return true if we got any response
            }
        }
        return false; // Return false if no SMS URL configured
    }

    public static function MikrotikSendSMS($router_name, $to, $message)
    {
        global $_app_stage, $client_m, $config;
        if ($_app_stage == 'demo') {
            return null;
        }
        if (!isset($client_m)) {
            $mikrotik = ORM::for_table('tbl_routers')->where('name', $router_name)->find_one();
            $iport = explode(":", $mikrotik['ip_address']);
            $client_m = new RouterOS\Client($iport[0], $mikrotik['username'], $mikrotik['password'], ($iport[1]) ? $iport[1] : null);
        }
        if(empty($config['mikrotik_sms_command'])){
            $config['mikrotik_sms_command'] = "/tool sms send";
        }
        $smsRequest = new RouterOS\Request($config['mikrotik_sms_command']);
        $smsRequest
            ->setArgument('phone-number', $to)
            ->setArgument('message', $message);
        $client_m->sendSync($smsRequest);
    }

    public static function sendWhatsapp($phone, $txt)
    {
        global $config;
        if (empty($txt)) {
            return false;
        }

        // GoWhatsApp (self-hosted go-whatsapp-multidevice-rest) — PRIMARY when enabled
        if (($config['gowhatsapp_enabled'] ?? '') === 'yes' && !empty($config['gowhatsapp_url']) && function_exists('gowhatsapp_hook_send')) {
            $result = call_user_func('gowhatsapp_hook_send', [$phone, $txt]);
            if ($result !== false) {
                return true;
            }
        }

        // ApiWap (Cloud) — fallback if GoWhatsApp not configured
        if (!empty($config['apiwap_enabled']) && $config['apiwap_enabled'] === 'yes' && !empty($config['apiwap_api_key']) && function_exists('apiwap_hook_send')) {
            $result = call_user_func('apiwap_hook_send', [$phone, $txt]);
            if ($result !== false) {
                return true;
            }
        }

        // GoWAHA (WAHA API) — fallback if ApiWap not configured
        if (!empty($config['gowaha_url']) && !empty($config['gowaha_key']) && function_exists('gowaha_hook_send')) {
            $result = call_user_func('gowaha_hook_send', [$phone, $txt]);
            if ($result !== false) {
                return true;
            }
        }

        // Other WhatsApp gateways via hook
        $hookResult = run_hook('send_whatsapp', [$phone, $txt]); #HOOK
        if ($hookResult !== false) {
            return true;
        }

        // Fallback: URL-based WhatsApp gateway
        if (!empty($config['wa_url'])) {
            $waurl = str_replace('[number]', urlencode(Lang::phoneFormat($phone)), $config['wa_url']);
            $waurl = str_replace('[text]', urlencode($txt), $waurl);
            $result = Http::getData($waurl);
            return !empty($result);
        }
        return false;
    }

    public static function sendEmail($to, $subject, $body)
    {
        global $config, $PAGES_PATH, $debug_mail;
        if (empty($body)) {
            return "";
        }
        if (empty($to)) {
            return "";
        }
        run_hook('send_email', [$to, $subject, $body]); #HOOK
        if (empty($config['smtp_host'])) {
            $attr = "";
            if (!empty($config['mail_from'])) {
                $attr .= "From: " . $config['mail_from'] . "\r\n";
            }
            if (!empty($config['mail_reply_to'])) {
                $attr .= "Reply-To: " . $config['mail_reply_to'] . "\r\n";
            }
            mail($to, $subject, $body, $attr);
        } else {
            $mail = new PHPMailer();
            $mail->isSMTP();
            if (isset($debug_mail) && $debug_mail == 'Dev') {
                $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            }
            $mail->Host       = $config['smtp_host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $config['smtp_user'];
            $mail->Password   = $config['smtp_pass'];
            $mail->SMTPSecure = $config['smtp_ssltls'];
            $mail->Port       = $config['smtp_port'];
            if (!empty($config['mail_from'])) {
                $mail->setFrom($config['mail_from']);
            }
            if (!empty($config['mail_reply_to'])) {
                $mail->addReplyTo($config['mail_reply_to']);
            }

            $mail->addAddress($to);
            $mail->Subject = $subject;

            if (!file_exists($PAGES_PATH . DIRECTORY_SEPARATOR . 'Email.html')) {
                if (!copy($PAGES_PATH . '_template' . DIRECTORY_SEPARATOR . 'Email.html', $PAGES_PATH . DIRECTORY_SEPARATOR . 'Email.html')) {
                    file_put_contents($PAGES_PATH . DIRECTORY_SEPARATOR . 'Email.html', Http::getData('https://raw.githubusercontent.com/hotspotbilling/phpnuxbill/master/pages_template/Email.html'));
                }
            }

            if (file_exists($PAGES_PATH . DIRECTORY_SEPARATOR . 'Email.html')) {
                $html = file_get_contents($PAGES_PATH . DIRECTORY_SEPARATOR . 'Email.html');
                $html = str_replace('[[Subject]]', $subject, $html);
                $html = str_replace('[[Company_Address]]', nl2br($config['address']), $html);
                $html = str_replace('[[Company_Name]]', nl2br($config['CompanyName']), $html);
                $html = str_replace('[[Body]]', nl2br($body), $html);
                $mail->isHTML(true);
                $mail->Body    = $html;
            } else {
                $mail->isHTML(false);
                $mail->Body    = $body;
            }
            if (!$mail->send()) {
                _log(Lang::T("Email not sent, Mailer Error: ") . $mail->ErrorInfo);
            }

            //<p style="font-family: Helvetica, sans-serif; font-size: 16px; font-weight: normal; margin: 0; margin-bottom: 16px;">
        }
    }

    public static function sendPackageNotification($customer, $package, $price, $message, $via)
    {
        global $ds, $config;
        if (empty($message)) {
            return "";
        }
        $msg = str_replace('[[name]]', $customer['fullname'], $message);
        $msg = str_replace('[[username]]', $customer['username'], $msg);
        $msg = str_replace('[[plan]]', $package, $msg);
        $msg = str_replace('[[package]]', $package, $msg);
        $msg = str_replace('[[price]]', Lang::moneyFormat($price), $msg);
        // Calculate bills and additional costs
        list($bills, $add_cost) = User::getBills($customer['id']);

        // Initialize note and total variables
        $note = "";
        $total = $price;

        // Add bills to the note if there are any additional costs
        if ($add_cost != 0) {
            foreach ($bills as $k => $v) {
                $note .= $k . " : " . Lang::moneyFormat($v) . "\n";
            }
            $total += $add_cost;
        }

        $msg = str_replace('[[bills]]', $note, $msg);
        $msg = str_replace('[[total]]', Lang::moneyFormat($total), $msg);

        $phone = $customer['phonenumber'];
        if (!empty($phone) && strlen($phone) > 5 && !empty($message)) {
            $sent = false;
            if ($via == 'sms' || $via == 'both') {
                $sent = Message::sendSMS($phone, $msg) || $sent;
            }
            if ($via == 'wa' || $via == 'both') {
                $sent = Message::sendWhatsapp($phone, $msg) || $sent;
            }
            if ($sent) {
                self::addToInbox($customer['id'], Lang::T('Package Notification'), $msg);
            }
        }
        return "$via: $msg";
    }

    public static function sendBalanceNotification($cust, $target, $balance, $balance_now, $message, $via)
    {
        global $config;
        $msg = str_replace('[[name]]', $target['fullname'] . ' (' . $target['username'] . ')', $message);
        $msg = str_replace('[[current_balance]]', Lang::moneyFormat($balance_now), $msg);
        $msg = str_replace('[[balance]]', Lang::moneyFormat($balance), $msg);
        $phone = $cust['phonenumber'];
        if (!empty($phone) && strlen($phone) > 5 && !empty($message)) {
            $sent = false;
            if ($via == 'sms' || $via == 'both') {
                $sent = Message::sendSMS($phone, $msg) || $sent;
            }
            if ($via == 'wa' || $via == 'both') {
                $sent = Message::sendWhatsapp($phone, $msg) || $sent;
            }
            if ($via == 'email') {
                self::sendEmail($cust['email'], '[' . $config['CompanyName'] . '] ' . Lang::T("Balance Notification"), $msg);
                $sent = true;
            }
            if ($sent) {
                self::addToInbox($cust['id'], Lang::T('Balance Notification'), $msg);
            }
        }
        return "$via: $msg";
    }

    public static function sendInvoice($cust, $trx)
    {
        global $config;
        $textInvoice = Lang::getNotifText('invoice_paid');
        $textInvoice = str_replace('[[company_name]]', $config['CompanyName'], $textInvoice);
        $textInvoice = str_replace('[[address]]', $config['address'], $textInvoice);
        $textInvoice = str_replace('[[phone]]', $config['phone'], $textInvoice);
        $textInvoice = str_replace('[[invoice]]', $trx['invoice'], $textInvoice);
        $textInvoice = str_replace('[[date]]', Lang::dateAndTimeFormat($trx['recharged_on'], $trx['recharged_time']), $textInvoice);
        $textInvoice = str_replace('[[trx_date]]', Lang::dateAndTimeFormat($trx['recharged_on'], $trx['recharged_time']), $textInvoice);
        if (!empty($trx['note'])) {
            $textInvoice = str_replace('[[note]]', $trx['note'], $textInvoice);
        }
        $gc = explode("-", $trx['method']);
        $textInvoice = str_replace('[[payment_gateway]]', trim($gc[0]), $textInvoice);
        $textInvoice = str_replace('[[payment_channel]]', trim($gc[1]), $textInvoice);
        $textInvoice = str_replace('[[type]]', $trx['type'], $textInvoice);
        $textInvoice = str_replace('[[plan_name]]', $trx['plan_name'], $textInvoice);
        $textInvoice = str_replace('[[plan_price]]',  Lang::moneyFormat($trx['price']), $textInvoice);
        $textInvoice = str_replace('[[name]]', $cust['fullname'], $textInvoice);
        $textInvoice = str_replace('[[note]]', $cust['note'], $textInvoice);
        $textInvoice = str_replace('[[user_name]]', $trx['username'], $textInvoice);
        $textInvoice = str_replace('[[user_password]]', $cust['password'], $textInvoice);
        $textInvoice = str_replace('[[username]]', $trx['username'], $textInvoice);
        $textInvoice = str_replace('[[password]]', $cust['password'], $textInvoice);
        $textInvoice = str_replace('[[expired_date]]', Lang::dateAndTimeFormat($trx['expiration'], $trx['time']), $textInvoice);
        $textInvoice = str_replace('[[footer]]', $config['note'], $textInvoice);

        // Check if we should send payment notification based on service type
        global $_notifmsg;
        $should_send = true;
        $service_type = isset($trx['type']) ? strtoupper($trx['type']) : 'HOTSPOT';
        
        if ($service_type == 'PPPOE') {
            // Check if sending to PPPoE users is enabled
            if (isset($_notifmsg['send_payment_to_pppoe']) && $_notifmsg['send_payment_to_pppoe'] != '1') {
                $should_send = false;
            }
        } else {
            // Check if sending to Hotspot users is enabled
            if (isset($_notifmsg['send_payment_to_hotspot']) && $_notifmsg['send_payment_to_hotspot'] != '1') {
                $should_send = false;
            }
        }
        
        if (!$should_send) {
            return; // Skip sending payment notification
        }

        // Prefer JSON-based notification setting (new system) over legacy DB config
        $payment_via = !empty($_notifmsg['payment_notification']) ? $_notifmsg['payment_notification'] : $config['payment_notification'];
        if ($payment_via == 'sms') {
            Message::sendSMS($cust['phonenumber'], $textInvoice);
        } else if ($payment_via == 'email') {
            self::sendEmail($cust['email'], '[' . $config['CompanyName'] . '] ' . Lang::T("Invoice") . ' #' . $trx['invoice'], $textInvoice);
        } else if ($payment_via == 'wa') {
            Message::sendWhatsapp($cust['phonenumber'], $textInvoice);
        } else if ($payment_via == 'both') {
            Message::sendSMS($cust['phonenumber'], $textInvoice);
            Message::sendWhatsapp($cust['phonenumber'], $textInvoice);
        }
    }


    public static function addToInbox($to_customer_id, $subject, $body, $from = 'System')
    {
        $v = ORM::for_table('tbl_customers_inbox')->create();
        $v->from = $from;
        $v->customer_id = $to_customer_id;
        $v->subject = $subject;
        $v->date_created = date('Y-m-d H:i:s');
        $v->body = nl2br($body);
        $v->save();
    }
}
