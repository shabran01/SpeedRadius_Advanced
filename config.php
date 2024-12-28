<?php

    define('APP_URL', 'https://snootylique.speedcomwifi.xyz');
    $_app_stage = 'Live';

    // Database PHPNuxBill
    $db_host	    = 'localhost';
    $db_user        = 'speedradius';
    $db_password	= 'KWEYU01@@';
    $db_name	    = 'snootylique';

    if($_app_stage!='Live'){
        error_reporting(E_ERROR);
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
    }else{
        error_reporting(E_ERROR);
        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 0);
    }
    