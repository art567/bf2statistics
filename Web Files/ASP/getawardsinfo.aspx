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

// Make sure we have a Player ID and its valid!
$pid = (isset($_GET['pid'])) ? $_GET['pid'] : false;
if (!$pid || !is_numeric($pid))
{
    print 'Invalid syntax!';
}
else
{
	// Import configuration
	$cfg = load_class('Config');
	
	// Connect to the database
	$connection = @mysql_connect($cfg->get('db_host'), $cfg->get('db_user'), $cfg->get('db_pass')) or die();
	@mysql_select_db($cfg->get('db_name'), $connection);
	
	// Prepare our output header
	$head = "O\n" .
		"H\tpid\tasof\n" .
		"D\t$pid\t" . time() . "\n" .
		"H\taward\tlevel\twhen\tfirst\n";
	
	// Query and get all of the Players awards
	$query = "SELECT awd, level, earned, first FROM awards WHERE id = {$pid} ORDER BY id";
	$result = mysql_query($query) or die(mysql_error());

	$count = 0;
	$out = "";
	if (mysql_num_rows($result))
	{
		while ($row = mysql_fetch_array($result))
		{
			if (($row['awd'] > 2000000) && ($row['awd'] < 3000000)) 
			{
				$first = $row['first'];  #medals
			}
			else 
			{
				$first = 0;
			}
			$out .= "D\t$row[awd]\t$row[level]\t$row[earned]\t$first\n";
		}
	}
	
	$num = strlen(preg_replace('/[\t\n]/','',$head));
	$num += strlen(preg_replace('/[\t\n]/','',$out));
	print $head;
	print $out . "$\t" . $num . "\t$";
	
	// Close database connection
	@mysql_close($connection);
}
?>