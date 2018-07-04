<?php
/**
 * Buy Sell
 *
 * PHP version 5
 *
 * @category   PHP
 * @package    buysell
 * @subpackage Core
 * @author     Agriya <info@agriya.com>
 * @copyright  2018 Agriya Infoway Private Ltd
 * @license    http://www.agriya.com/ Agriya Infoway Licence
 * @link       http://www.agriya.com
 */
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$isDebug = array_key_exists('debug', $_REQUEST);

if ($isDebug) {
    ini_set('display_errors', 1);
    error_reporting(1);
}
else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

define('PATH_INSTALL', str_replace("\\", "/", realpath(dirname(__FILE__)."/../")));
define('API_PATH', 'http://ni.dev.develag.com/api/');

/*
 * Address timeout limits
 */
if (!ini_get('safe_mode'))
    set_time_limit(3600);

/*
 * Handle fatal errors with AJAX
 */
register_shutdown_function('installerShutdown');
function installerShutdown()
{
    global $installer;
    $error = error_get_last();
    if ($error['type'] == 1) {
        header('HTTP/1.1 500 Internal Server Error');
        $errorMsg = htmlspecialchars_decode(strip_tags($error['message']));
        echo $errorMsg;
        if (isset($installer))
            $installer->log('Fatal error: %s on line %s in file %s', $errorMsg, $error['line'], $error['file']);
        exit;
    }
}

/*
 * Bootstrap the installer
 */
require_once 'InstallerException.php';
require_once 'InstallerRewrite.php';
require_once 'Installer.php';

try {
    $installer = new Installer();
    $installer->cleanLog();
    $installer->log('Host: %s', php_uname());
    $installer->log('PHP version: %s', PHP_VERSION);
    $installer->log('Server software: %s', isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : 'Unknown');
    $installer->log('Operating system: %s', PHP_OS);
    $installer->log('Memory limit: %s', ini_get('memory_limit'));
    $installer->log('Max execution time: %s', ini_get('max_execution_time'));
}
catch (Exception $ex) {
    $fatalError = $ex->getMessage();
}
?>