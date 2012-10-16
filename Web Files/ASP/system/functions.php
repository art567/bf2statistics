<?php
/*
| ---------------------------------------------------------------
| Function: load_class()
| ---------------------------------------------------------------
|
| This function is used to load and store core classes statically 
| that need to be loaded for use, but not reset next time the class
| is called.
|
| @Param: (String) $className - Class needed to be loaded / returned
| @Return: (Object) - Returns the loaded class
|
*/
    function load_class($className, $new_instance = FALSE)
    {
		// Check for new instance of class
		if($new_instance == FALSE)
		{
			// Check the registry for the class, If its there, then return the class
			$loaded = Registry::singleton()->load($className);
			if($loaded !== NULL) return $loaded;
		}

        // Include our file. If it doesnt exists, class is un-obtainable.
		$file = SYSTEM_PATH . DS . 'core' . DS . $className .'.php';
        require($file);

        //  Initiate the new class into a variable
        try{
			// Create and then Store this new object in the registery
			$Obj = new $className();
			Registry::singleton()->store($className, $Obj);
        }
        catch(Exception $e) {
            $Obj = FALSE;
        }

        // return the object.
        return $Obj;
    }
	
/*
| ---------------------------------------------------------------
| Function: load_database()
| ---------------------------------------------------------------
|
| This function is used to load and store the database connection
| statically 
|
| @Return: (Object) - Returns the database class/connection
|
*/
    function load_database()
    {

		// Check the registry for the class, If its there, then return the class
		$loaded = Registry::singleton()->load('Database');
		if($loaded !== NULL) return $loaded;

        // Include our file. If it doesnt exists, class is un-obtainable.
		$file = SYSTEM_PATH . DS . 'database' . DS . 'driver.php';
        require_once($file);

        //  Initiate the new class into a variable
        try{
			// Create and then Store this new object in the registery
			$Obj = new Database();
			Registry::singleton()->store('Database', $Obj); 
        }
        catch(Exception $e) {
            $Obj = FALSE;
        }

        // return the object.
        return $Obj;
    }
	
/*
| ---------------------------------------------------------------
| Method: config()
| ---------------------------------------------------------------
|
| This function is used to return a config value from a config
| file.
|
| @Param: (String) $item - The config item we are looking for
| @Return: (Mixed) - Returns the config vaule of $item
|
*/
    function config($item)
    {
        $Config = load_class('Config');		
        return $Config->get($item);
    }
	
/*
| ---------------------------------------------------------------
| Function: sec2hms()
| ---------------------------------------------------------------
|
| Converts a timestamp to how many days, hours, mintues left
| Thanks to: http://www.laughing-buddha.net/php/lib/sec2hms/
|
| @Param: (Int) $sec - The timestamp
| @Return (String) The array of data
|
*/
    function sec2hms($sec, $padHours = true) 
    {
        // start with a blank string
        $hms = "";

        // do the hours first: there are 3600 seconds in an hour, so if we divide
        // the total number of seconds by 3600 and throw away the remainder, we're
        // left with the number of hours in those seconds
        $hours = intval(intval($sec) / 3600); 

        // add hours to $hms (with a leading 0 if asked for)
        $hms .= ($padHours) ? str_pad($hours, 2, "0", STR_PAD_LEFT). ":" : $hours. ":";

        // dividing the total seconds by 60 will give us the number of minutes
        // in total, but we're interested in *minutes past the hour* and to get
        // this, we have to divide by 60 again and then use the remainder
        $minutes = intval(($sec / 60) % 60); 

        // add minutes to $hms (with a leading 0 if needed)
        $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ":";

        // seconds past the minute are found by dividing the total number of seconds
        // by 60 and using the remainder
        $seconds = intval($sec % 60); 

        // add seconds to $hms (with a leading 0 if needed)
        $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

        // done!
        return $hms;
    }
	
/*
| ---------------------------------------------------------------
| Function: format_time()
| ---------------------------------------------------------------
|
| Converts a timestamp to a human readable time format
|
| @Param: (Int) $sec - The timestamp
| @Return (String) The array of data
|
*/
	function format_time($seconds)
	{
		// Get our seconds to hours:minutes:seconds
		$time = sec2hms($seconds, false);
		
		// Explode the time
		$time = explode(':', $time);
		
		// Hour corrections
		$set = '';
		if($time[0] > 0)
		{
			// Set days if viable
			if($time[0] > 23)
			{
				$days = floor($time[0] / 24);
				$time[0] = $time[0] - ($days * 24);
				$set .= ($days > 1) ? $days .' Days' : $days .' Day';
				if($time[0] > 0) $set .= ',';
			}
			$set .= ($time[0] > 1) ? $time[0] .' Hours' : $time[0] .' Hour';
		}
		if($time[1] > 0)
		{
			$set .= ($time[0] > 0) ? ', ' : '';
			$set .= ($time[1] > 1) ? $time[1] .' Minutes' : $time[1] .' Minute';
		}
		
		return $set;
	}
	
/*
| ---------------------------------------------------------------
| Function: microtime_float()
| ---------------------------------------------------------------
|
| Returns the absolute microtime in a float
|
*/
	function microtime_float() 
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
	
/*
| ---------------------------------------------------------------
| Method: get_database_stats()
| ---------------------------------------------------------------
|
| This function is used to return an array of query data
|
| @Param: (Int) $mode- What data do we want?
|
*/
	function get_database_stats($mode = 0)
	{
		$stats = load_class('Database')->statistics();
		switch($mode)
		{
			case 1:
				$data = $stats['total_queries'];
				break;
				
			case 2:
				$data = $stats['total_time'];
				break;
				
			default:
				$data = $stats;
				break;
		}
		return $data;
	}

/*
| ---------------------------------------------------------------
| Method: redirect()
| ---------------------------------------------------------------
|
| This function is used to easily redirect and refresh pages
|
| @Param: (String) $url - Where were going
| @Param: (Int) $wait - How many sec's we wait till the redirect.
| @Return: (None)
|
*/
    function redirect($url, $wait = 0)
    {
        // Check for refresh or straight redirect
        if($wait >= 1)
        {
            header("Refresh:". $wait .";url=". $url);
        }
        else
        {
            header("Location: ".$url);
            die();
        }
    }

/*
| ---------------------------------------------------------------
| Method: getDataTables()
| ---------------------------------------------------------------
|
| This function returns an array of all bf2stats table names
|
*/	
	function getDataTables()
	{
		return array(
			'army',
			'awards',
			'kills',
			'kits',
			'mapinfo',
			'maps',
			'player',
			'player_history',
			'round_history',
			'servers',
			'unlocks',
			'vehicles',
			'weapons',
		);
	}

/*
| ---------------------------------------------------------------
| Method: isIPInNet()
| ---------------------------------------------------------------
|
| Notes:
|		Host address and subnets are supported, use x.x.x.x/y standard notation.
|		Addresses without subnet (ie, x.x.x.x) are assumed to be a single HOST
|		An address of 0.0.0.0/0 matches ALL HOSTS (ie, disbales check)
|		
|	$auth_hosts = array(
|		"127.0.0.1",
|		"10.0.0.0/8",
|		"172.16.0.0/12",
|		"192.168.0.0/16"
|	);
|
*/

function isIPInNet($ip, $net, $mask) 
{
	$lnet = ip2long($net);
	$lip = ip2long($ip);
	$binnet = str_pad( decbin($lnet), 32, "0", STR_PAD_LEFT );
	$firstpart = substr($binnet, 0, $mask);
	$binip = str_pad( decbin($lip), 32, "0", STR_PAD_LEFT );
	$firstip = substr($binip, 0, $mask);
	
	return( strcmp($firstpart, $firstip) == 0 );
}

/*
| ---------------------------------------------------------------
| Method: isIPInNetArray()
| ---------------------------------------------------------------
|
| This function checks if an ip is in an array of nets (ip and mask)
|
*/
function isIpInNetArray($theip,$thearray) 
{
	$exit_c = false;
	
	if(is_array($thearray)) 
	{
		foreach($thearray as $subnet) 
		{
			// Match all
			if($subnet == '0.0.0.0' || $subnet == '0.0.0.0/0') 
			{
				$exit_c = true;
				break;
			}
			
			if(strpos($subnet, "/") === false)
			{
				$subnet .= "/32";
			}
			
			list($net,$mask) = explode("/",$subnet);
			if(isIPInNet($theip,$net,$mask))
			{
				$exit_c = true;
				break;
			}
		}
	}
	return($exit_c);
}

/*
| ---------------------------------------------------------------
| Method: checkIpAuth()
| ---------------------------------------------------------------
|
| Check to see if an IP is authorized access
|
*/
function checkIpAuth($chkhosts) 
{
	// Get our remote connections IP
	if(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] != "") 
	{
		$ip_s = $_SERVER['REMOTE_ADDR'];
	}

	// Check for authorized IP
	if ($ip_s != "" && isIPInNetArray($ip_s, $chkhosts))
	{
		return 1;	// Authorised HOST IP
	}
	else
	{
		return 0;	// UnAuthorised HOST IP
	}
}

/*
| ---------------------------------------------------------------
| Method: checkPrivateIp()
| ---------------------------------------------------------------
|
| Checks if the givin IP is a Private (local) IP
|
*/
function checkPrivateIp($ip_s) 
{
	// Define Private IPs
	$privateIPs = array();
	$privateIPs[] = '10.0.0.0/8';
	$privateIPs[] = '127.0.0.0/8';
	$privateIPs[] = '172.16.0.0/12';
	$privateIPs[] = '192.168.0.0/16';
	
	if ($ip_s != "" && isIPInNetArray($ip_s, $privateIPs))
	{
		return 1;	// Private IP
	}
	else
	{
		return 0;	// Public/Other IP
	}
}

/*
| ---------------------------------------------------------------
| Method: qoute_smart()
| ---------------------------------------------------------------
|
| Quote variable to make safe (SQL Injection protection code)
|
*/
function quote_smart($value) 
{
    // Stripslashes
    if(get_magic_quotes_gpc()) 
	{
        $value = stripslashes($value);
    }
	
    // Quote if not integer
    if(!is_numeric($value)) 
	{
        $value = mysql_real_escape_string($value);
    }
    return $value;
}

/*
| ---------------------------------------------------------------
| Method: getDbVer()
| ---------------------------------------------------------------
|
| Get Database Version
|
*/
function getDbVer() 
{
	$cfg = load_class('Config');
	$DB = load_database();
	$curver = '0.0.0';
	
	// Can only query the database when its online ;)
	if($DB->status() == 1) 
	{
		$query = "SELECT dbver FROM _version";
		$curver = $DB->query($query, TRUE)->fetch_column();
		if($curver == false) $curver = '0.0.0';
	}
	return $curver;
}

/*
| ---------------------------------------------------------------
| Method: verCmp()
| ---------------------------------------------------------------
|
| Converts the DB version from a float to INT for comparison
|
*/
function verCmp($ver) 
{
	$ver_arr = explode(".", $ver);
	
	$i = 1;
	$result = 0;
	foreach($ver_arr as $vbit) 
	{
		$result += $vbit * $i;
		$i = $i / 100;
	}
	return $result;
}

/*
| ---------------------------------------------------------------
| Method: ErrorLog()
| ---------------------------------------------------------------
|
| Logs stats errors
|
*/
function ErrorLog($msg, $lvl)
{
	$Config = load_class('Config');
	
	switch($lvl) 
	{
		case -1:
			$lvl_txt = 'INFO: ';
			break;
		case 0:
			$lvl_txt = 'SECURITY: ';
			break;
		case 1:
			$lvl_txt = 'ERROR: ';
			break;
		case 2:
			$lvl_txt = 'WARNING: ';
			break;
		default:
			$lvl_txt = 'NOTICE: ';
			break;
	}
	
	if($lvl <= $Config->get('debug_lvl'))
	{
		$err_msg = date('Y-m-d H:i:s')." -- ".$lvl_txt.$msg."\n";
		$log = SYSTEM_PATH . DS . 'logs' . DS . 'stats_debug.log';
		$file = @fopen($log, 'a');
		@fwrite($file, $err_msg);
		@fclose($file);
	}
}

/*
| ---------------------------------------------------------------
| Method: checkSQLResult()
| ---------------------------------------------------------------
|
| Used to check SQL Results
|
*/
function checkSQLResult($result, $query) 
{
	if(!$result) 
	{
		$msg  = 'ERROR: ' . mysql_error() . "\n";
		$msg .= 'Query String: ' . $query;
		ErrorLog($msg, 1);
		return 1;
	}
	else
	{
		return 0;
	}
}

/*
| ---------------------------------------------------------------
| Method: get_ext_ip()
| ---------------------------------------------------------------
|
| Gets the IP Address of the connecting machine
|
*/
function get_ext_ip() 
{
	return $_SERVER['REMOTE_ADDR'];
}

/*
| ---------------------------------------------------------------
| Method: getPageContents()
| ---------------------------------------------------------------
|
| Uses either file() or CURL to get the contents of a page
|
*/
function getPageContents($url)
{	
	// Try file() first
	if( function_exists('file') && function_exists('fopen') && ini_get('allow_url_fopen') ) 
	{
		ini_set("user_agent","GameSpyHTTP/1.0");
		$results = @file($url);
	}
	
	// either there was no function, or it failed -- try curl
	if( !$results && function_exists('curl_exec') ) 
	{
		$curl_handle = curl_init();
		curl_setopt($curl_handle, CURLOPT_URL, $url);
		curl_setopt($curl_handle, CURLOPT_USERAGENT, "GameSpyHTTP/1.0");
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 1);
		curl_setopt($curl_handle, CURLOPT_TIMEOUT, 10);
		$results = curl_exec($curl_handle);
		$err = curl_error($curl_handle);
		if( $err != '' ) 
		{
			return false;
		}
		$results = explode("\n",trim($results));
		curl_close($curl_handle);
	}
	
	// still nothing, forgetd a'bout it
	if( !$results ) return false;
	return $results;
}

// TO BE REMOVED
function chkPath($path) 
{
	if(($path{strlen($path)-1} != "/") && ($path{strlen($path)-1} != "\\")) 
	{
		return $path . "/";
	}
	else
	{
		return $path;
	}
}
?>