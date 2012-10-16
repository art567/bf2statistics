<?php

/*
    Copyright (C) 2006-2012  BF2Statistics

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
 
/*
| ---------------------------------------------------------------
| Define ROOT and system paths
| ---------------------------------------------------------------
*/
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));
define('SYSTEM_PATH', ROOT . DS . 'system');

/*
| ---------------------------------------------------------------
| Require the needed scripts to launch the system
| ---------------------------------------------------------------
*/
require(SYSTEM_PATH . DS . 'core'. DS .'Registry.php');
require(SYSTEM_PATH . DS . 'functions.php');

// Set Error Reporting
error_reporting(E_ALL);
ini_set("log_errors", "1");
ini_set("error_log", SYSTEM_PATH . DS . 'logs' . DS . 'php_errors.log');
ini_set("display_errors", "0");
 
//Disable Zlib Compression
ini_set('zlib.output_compression', '0');

// Make sure we have a Nick to go by
$nick = (isset($_GET['nick'])) ? $_GET['nick'] : false;
if (!$nick || $nick == '')
{
    print 'Invalid syntax!';
}
else
{
	$head = "O\n" .
		"H\tasof\n" .
		"D\t" . time() . "\n" .
		"H\tn\tpid\tnick\tscore\n";
	
	$num = strlen(preg_replace('/[\t\n]/','',$head));
	print $head;

	// Import configuration
	$cfg = load_class('Config');

	// Establish database connection
	$connection = @mysql_connect($cfg->get('db_host'), $cfg->get('db_user'), $cfg->get('db_pass'));
	@mysql_select_db($cfg->get('db_name'), $connection);
	
	$query = "SELECT id, name, score FROM player WHERE name LIKE '%".quote_smart($nick)."%'";
	$result = mysql_query($query) or die(mysql_error());

	$num = 1;
	$count = 31;
	$out = "";

	if (mysql_num_rows($result))
	{
		while ($row = mysql_fetch_array($result))
		{
			$count += strlen($num) + strlen($row['id']) + $row['name'] + $row['score'];
			$out .= "D\t" . $num++ . "\t" . $row['id'] . "\t" . $row['name'] . "\t" . $row['score'] . "\n";
		}
	}

	$num += strlen(preg_replace('/[\t\n]/','',$out));
	print $out . "$\t" . $num . "\t$";
	
	@mysql_close($connection);
}
?>