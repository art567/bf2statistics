<?php 
/* 
| --------------------------------------------------------------
| BF2 Statistics Admin Util
| --------------------------------------------------------------
| Author:       Steven Wilson 
| Copyright:    Copyright (c) 2012
| License:      GNU GPL v3
| ---------------------------------------------------------------
|
| I would like to take a moment and Thank all of those involved
| in the creation if BF2statistics. It is an amazing system in
| which I, myself have enjoyed so many hours on. The core of This 
| admin Util was written by myself, But alot of code was borrowed 
| from the original ASP... I take no credit for creating the 
| Original bf2statistics ASP.
|
*/

/*
| ---------------------------------------------------------------
| Define that we are here in the BF2 Admin area, prevents direct 
| linking of files, Also define ROOT and system paths
| ---------------------------------------------------------------
*/
define('BF2_ADMIN', 1);
define('CODE_VER', '1.5.1');
define('CODE_VER_DATE', '2012-04-24');
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));
define('SYSTEM_PATH', ROOT . DS . 'system');
define('TIME_START', microtime(true));

/*
| ---------------------------------------------------------------
| Set Error Reporting
| ---------------------------------------------------------------
*/
error_reporting(E_ALL);
ini_set("log_errors", "1");
ini_set("error_log", SYSTEM_PATH . DS . 'logs' . DS . 'php_errors.log');
ini_set("display_errors", "1");

/*
| ---------------------------------------------------------------
| Require the needed scripts to launch the system
| ---------------------------------------------------------------
*/
require(SYSTEM_PATH . DS . 'core'. DS .'Registry.php');
require(SYSTEM_PATH . DS . 'functions.php');

/*
| ---------------------------------------------------------------
| Load the controller, which in turn loads the current task
| ---------------------------------------------------------------
*/
$Controller = load_class('Controller');
$Controller->Init();
?>