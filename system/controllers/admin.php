<?php

/**
 *  PHP Mikrotik Billing (https://github.com/hotspotbilling/phpnuxbill/)
 *  by https://t.me/ibnux
 **/

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Pragma: no-cache");

if (Admin::getID()) {
    r2(U . 'dashboard', "s", Lang::T("You are already logged in"));
}

if (isset($routes['1'])) {
    $do = $routes['1'];
} else {
    $do = 'login-display';
}

switch ($do) {
    case 'post':
        $username = _post('username');
        $password = _post('password');
        //csrf token
        $csrf_token = _post('csrf_token');
        if (!Csrf::check($csrf_token)) {
            _alert(Lang::T('Invalid or Expired CSRF Token') . ".", 'danger', "admin");
        }
        run_hook('admin_login'); #HOOK
        if ($username != '' and $password != '') {
            $d = ORM::for_table('tbl_users')->where('username', $username)->find_one();
            if ($d) {
                $d_pass = $d['password'];
                if (Password::_verify($password, $d_pass) == true) {
                    $_SESSION['aid'] = $d['id'];
                    $token = Admin::setCookie($d['id']);
                    $d->last_login = date('Y-m-d H:i:s');
                    $d->save();
                    _log($username . ' ' . Lang::T('Login Successful'), $d['user_type'], $d['id']);
                    if ($isApi) {
                        if ($token) {
                            showResult(true, Lang::T('Login Successful'), ['token' => "a." . $token]);
                        } else {
                            showResult(false, Lang::T('Invalid Username or Password'));
                        }
                    }
                    _alert(Lang::T('Login Successful'), 'success', "dashboard");
                } else {
                    _log($username . ' ' . Lang::T('Failed Login'), $d['user_type']);
                    _alert(Lang::T('Invalid Username or Password') . ".", 'danger', "admin");
                }
            } else {
                _alert(Lang::T('Invalid Username or Password') . "..", 'danger', "admin");
            }
        } else {
            _alert(Lang::T('Invalid Username or Password') . "...", 'danger', "admin");
        }

        break;
        
    case 'forgot-password':
        global $config, $CACHE_PATH, $db_pass;
        $step = _req('step', 0);
        $otpPath = $CACHE_PATH . File::pathFixer('/admin_forgot/');
        
        if ($step == '-1') {
            $_COOKIE['admin_forgot_username'] = '';
            setcookie('admin_forgot_username', '', time() - 3600, '/');
            $step = 0;
        }

        if (!empty($_COOKIE['admin_forgot_username']) && in_array($step, [0, 1])) {
            $step = 1;
            $_POST['username'] = $_COOKIE['admin_forgot_username'];
        }

        if ($step == 1) {
            $username = _post('username');
            if (!empty($username)) {
                $ui->assign('username', $username);
                if (!file_exists($otpPath)) {
                    mkdir($otpPath);
                    touch($otpPath . 'index.html');
                }
                setcookie('admin_forgot_username', $username, time() + 3600, '/');
                $admin = ORM::for_table('tbl_users')->selects(['phone', 'email', 'fullname'])->where('username', $username)->find_one();
                if ($admin) {
                    $otpPath .= sha1($username . $db_pass) . ".txt";
                    if (file_exists($otpPath) && time() - filemtime($otpPath) < 600) {
                        $sec = 600 - (time() - filemtime($otpPath));
                        $ui->assign('notify_t', 's');
                        $ui->assign('notify', Lang::T("Verification Code already sent. Please wait") . " $sec " . Lang::T("seconds"));
                    } else {
                        $otp = mt_rand(100000, 999999);
                        file_put_contents($otpPath, $otp);
                        
                        // Send via email if available
                        if (!empty($admin['email']) && !empty($config['smtp_host'])) {
                            Message::sendEmail(
                                $admin['email'],
                                $config['CompanyName'] . ' - ' . Lang::T("Admin Password Reset Code") . ' : ' . $otp,
                                Lang::T("Dear") . ' ' . $admin['fullname'] . ",\n\n" . 
                                Lang::T("Your admin password reset verification code is") . ': <b>' . $otp . '</b>\n\n' .
                                Lang::T("This code will expire in 10 minutes") . ".\n\n" .
                                Lang::T("If you did not request this, please ignore this message") . ".\n\n" .
                                $config['CompanyName']
                            );
                        }
                        
                        // Send via WhatsApp if available
                        if (!empty($admin['phone']) && !empty($config['wa_url'])) {
                            $waMessage = $config['CompanyName'] . "\n";
                            $waMessage .= Lang::T("Admin Password Reset Code") . ": " . $otp . "\n";
                            $waMessage .= Lang::T("This code expires in 10 minutes") . ".";
                            Message::sendWhatsapp($admin['phone'], $waMessage);
                        }
                        
                        $ui->assign('notify_t', 's');
                        $ui->assign('notify', Lang::T("If your username is found, verification code has been sent to your email/WhatsApp"));
                    }
                } else {
                    // Username not found - show same message for security
                    $ui->assign('notify_t', 's');
                    $ui->assign('notify', Lang::T("If your username is found, verification code has been sent to your email/WhatsApp"));
                }
            } else {
                $step = 0;
            }
        } else if ($step == 2) {
            $username = _post('username');
            $otp_code = _post('otp_code');
            if (!empty($username) && !empty($otp_code)) {
                $otpPath .= sha1($username . $db_pass) . ".txt";
                if (file_exists($otpPath) && time() - filemtime($otpPath) <= 600) {
                    $otp = file_get_contents($otpPath);
                    if ($otp == $otp_code) {
                        $newPassword = mt_rand(100000, 999999);
                        $admin = ORM::for_table('tbl_users')->where('username', $username)->find_one();
                        if ($admin) {
                            $admin->password = Password::_make($newPassword);
                            $admin->save();
                            
                            $ui->assign('username', $username);
                            $ui->assign('new_password', $newPassword);
                            $ui->assign('admin_name', $admin['fullname']);
                            $ui->assign('notify_t', 's');
                            $ui->assign('notify', Lang::T("Password has been reset successfully"));
                            
                            // Clean up OTP file
                            if (file_exists($otpPath)) {
                                unlink($otpPath);
                            }
                            setcookie('admin_forgot_username', '', time() - 3600, '/');
                            
                            _log("Password reset completed for admin: $username", 'Admin', $admin['id']);
                        } else {
                            r2(U . 'admin/forgot-password&step=1', 'e', Lang::T('Invalid username or verification code'));
                        }
                    } else {
                        r2(U . 'admin/forgot-password&step=1', 'e', Lang::T('Invalid username or verification code'));
                    }
                } else {
                    if (file_exists($otpPath)) {
                        unlink($otpPath);
                    }
                    r2(U . 'admin/forgot-password&step=1', 'e', Lang::T('Verification code expired or invalid'));
                }
            } else {
                r2(U . 'admin/forgot-password&step=1', 'e', Lang::T('Username and verification code are required'));
            }
        }

        // Clean up old OTP files (older than 1 hour)
        if (file_exists($CACHE_PATH . '/admin_forgot/')) {
            $pth = $CACHE_PATH . File::pathFixer('/admin_forgot/');
            $fs = scandir($pth);
            foreach ($fs as $file) {
                if (is_file($pth . $file) && time() - filemtime($pth . $file) > 3600) {
                    unlink($pth . $file);
                }
            }
        }

        $ui->assign('step', $step);
        $ui->assign('_title', Lang::T('Admin Forgot Password'));
        $ui->display('admin-forgot-password.tpl');
        break;
        
    default:
        run_hook('view_login'); #HOOK
        $csrf_token = Csrf::generateAndStoreToken();
        $ui->assign('csrf_token', $csrf_token);
        $ui->display('admin-login.tpl');
        break;
}
