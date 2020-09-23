<?php

define('TWSAPP_DIR', 'assets/snippets/market/');

define('TWSAPP_BASE_PATH', MODX_BASE_PATH . TWSAPP_DIR);

define('TWS_APP_SESSION_DATAKEY', 'twsapp_data');



date_default_timezone_set('Australia/Melbourne'); // date functions return melbourne time



include TWSAPP_BASE_PATH . 'common.php'; // common functions for application

include TWSAPP_BASE_PATH . 'config.php'; // Main application config data

//require_once "/home/rokebyma/phpGrid_Enterprise/conf.php"; // phpGrid support



/**

 * Auto load our class files from our app area

 */

function twsapp_autoloader($class)

{

    $file = TWSAPP_BASE_PATH . 'classes/' . strtolower($class) . '.class.php';

    if (file_exists($file)) {

        include $file;
    }
}

spl_autoload_register('twsapp_autoloader');



$modx->regClientScript('assets/snippets/market/js/myapp.js'); // common application JS



$modx->regClientScript('assets/snippets/market/js/moment.js'); // common application JS

$modx->regClientScript('assets/snippets/market/js/jquery.datetimepicker.js'); // common application JS



$modx->regClientCSS('assets/snippets/market/css/theme.blue.css'); //phpGrid theme

$modx->regClientCSS('assets/snippets/market/css/appstyle.css'); //phpGrid theme

$modx->regClientCSS('assets/snippets/market/css/jquery.datetimepicker.css'); //datetimepicker css



//$user = $modx->userLoggedIn();

// Launch application if user is in suitable group

if ($modx->isMemberOfWebGroup(array('Market Admin'))) {

    $Admin = new Admin($config);
}


/*

function check() {

// get a list of all class methods

$methods= get_class_methods('Admin');



$file = TWSAPP_BASE_PATH.'classes/admin.class.php';

$code = file_get_contents($file);

foreach($methods as $method) {

$search = '$this->'.$method;

if(!strstr($code,$search)){

echo $search ."<br>\n";

}



}

}

check();

 */

TWS::clearFlashData(); // clear all application session data as definitely not needed after here!
