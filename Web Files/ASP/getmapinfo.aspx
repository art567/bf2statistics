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

// Make sure we have the needed params
$pid 	 = (isset($_GET['pid'])) ? $_GET['pid'] : 0;
$mapid 	 = (isset($_GET['mapid'])) ? $_GET['mapid'] : 0;
$mapname = (isset($_GET['mapname'])) ? $_GET['mapname'] : '';
$limit	 = (isset($_GET['customonly'])) ? $_GET['customonly'] : 0;

// Import configuration
$cfg = load_class('Config');

// Limit results to custom maps ONLY
$maplimit = ($limit == 1) ? " AND id >= " . $cfg->get('game_custom_mapid') : '';

// Make sure our params is valid
if (!is_numeric($pid) || !is_numeric($mapid) || !is_numeric($limit)) 
{
	die("Invalid Parameters!");
}

// Check for valid map ID
if ($mapid && !is_numeric($mapid)) 
{
	print 'Invalid syntax!';
} 
elseif ($pid && is_numeric($pid)) 
{
	// Establish a database connection
	$connection = @mysql_connect($cfg->get('db_host'), $cfg->get('db_user'), $cfg->get('db_pass'));
	@mysql_select_db($cfg->get('db_name'), $connection);
	
	$query = "SELECT m.*, mi.name AS mapname" .
		"\nFROM maps m JOIN mapinfo mi ON m.mapid = mi.id" .
		"\nWHERE m.id = {$pid}" .
		"\nORDER BY mapid";
	$result = mysql_query($query) or die(mysql_error());

	if (!mysql_num_rows($result)) 
	{
		$out = "E\n" .
			"H\terr\n" .
			"D\tPlayer Map Data Not Found!\n";
			
		$num = strlen(preg_replace('/[\t\n]/','',$out));
		print $out . "$\t" . $num . "\t$";
	} 
	else 
	{
		$out = "O\n" .
			"H\tmapid\tmapname\ttime\twin\tloss\tbest\tworst\n";
			
		while($row = mysql_fetch_array($result)) 
		{
			$out .= "D\t$row[mapid]\t$row[mapname]\t$row[time]\t$row[win]\t$row[loss]\t$row[best]\t$row[worst]\n";
		}
		
		$num = strlen(preg_replace('/[\t\n]/','',$out));
		print $out . "$\t" . $num . "\t$";
	}
	@mysql_close($connection);
} 
else 
{
	$connection = @mysql_connect($cfg->get('db_host'), $cfg->get('db_user'), $cfg->get('db_pass'));
	@mysql_select_db($cfg->get('db_name'), $connection);
	
	if($mapid) 
	{
		$query = "SELECT * FROM mapinfo WHERE id = {$mapid} {$maplimit}";
	}
	elseif ($mapname) 
	{
		$query = "SELECT * FROM mapinfo WHERE name = '".quote_smart($mapname)."'{$maplimit}";
	} 
	else 
	{
		$query = "SELECT * FROM mapinfo WHERE name <> ''{$maplimit} ORDER BY id";
	}
	$result = mysql_query($query) or die(mysql_error());

	if(!mysql_num_rows($result)) 
	{
		$out = "E\n" .
			"H\terr\n" .
			"D\tMap Data Not Found!\n";
			
		$num = strlen(preg_replace('/[\t\n]/','',$out));
		print $out . "$\t" . $num . "\t$";
	} 
	else 
	{
		$out = "O\n" .
			"H\tmapid\tname\tscore\ttime\ttimes\tkills\tdeaths\n";
		
		while($row = mysql_fetch_array($result)) 
		{
			$out .= "D\t$row[id]\t$row[name]\t$row[score]\t$row[time]\t$row[times]\t$row[kills]\t$row[deaths]\n";
		}
		
		$num = strlen(preg_replace('/[\t\n]/','',$out));
		print $out . "$\t" . $num . "\t$";
	}
	@mysql_close($connection);
}
?>