<?php
define('TWS_BASE_PATH', MODX_BASE_PATH . 'assets/extends/tws/');

function tws_autoloader($class)
{
    $file = TWS_BASE_PATH . strtolower($class) . '.class.php';
    if (file_exists($file)) {
        include $file;
    }

}

spl_autoload_register('tws_autoloader');

DBX::connect($modx); // set DBX to use modx->db->conn now we have mysqli
