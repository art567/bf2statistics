<?php
/* 
| --------------------------------------------------------------
| BF2 Statistics Admin Util
| --------------------------------------------------------------
| Author:       Steven Wilson 
| Copyright:    Copyright (c) 2012
| License:      GNU GPL v3
| ---------------------------------------------------------------
| Class: Auth()
| ---------------------------------------------------------------
|
*/

class Auth
{
    // The database connection
    protected $DB, $Config;
    
    // Clients IP address
    public $remote_ip;
	
	// Session started?
	protected static $started;

/*
| ---------------------------------------------------------------
| Constructer
| ---------------------------------------------------------------
|
| Initiates the user sessions and such
|
*/

    public function __construct()
    {
        // Setup the DB connections
        $this->DB = load_database();
		$this->Config = load_class('Config');
        $this->remote_ip = $_SERVER['REMOTE_ADDR'];
		
		// Start the session if it isnt started
		if(!self::$started)
		{
			session_start();
			self::$started = true;
		}
    }

/*
| ---------------------------------------------------------------
| Function: load_user()
| ---------------------------------------------------------------
|
| This method checks to see if the user is logged in by session.
| If not then a username, id, and account level are set at guest.
| Also checks for login expire time.
|
*/

    public function check_session()
    {
        // Session isnt set
		if(!isset($_SESSION['adminAuth'])) 
		{
			return false;
		}
		
		// If the password set is wrong
		elseif($_SESSION['adminAuth'] != sha1($this->Config->get('admin_user').':'.$this->Config->get('admin_pass'))) 
		{
			return false;
		}
		
		// If the session time is expired
		elseif($_SESSION['adminTime'] < time() - (30*60)) 
		{
			return false;
		}
		
		// Everything is good, update the session time
		else
		{
			// Update Session Time
			$_SESSION['adminTime'] = time();
			return true;
		}
    }

/*
| ---------------------------------------------------------------
| Function: login()
| ---------------------------------------------------------------
|
| The main login script!
|
| @Param: (String) $username - The username logging in
| @Param: (String) $password - The unencrypted password
| @Return (Bool) True upon success, FALSE otherwise
|
*/

    public function login($username, $password)
    {
        // Initialize or retrieve the current values for the login variables
		if(!isset($_POST['loginAttempts'])) $_POST['loginAttempts'] = 1;
		$loginAttempts = $_POST['loginAttempts'];
		
		// If the posted username and/or password doesnt match whats set in config.
		if($username != $this->Config->get('admin_user') || $password != $this->Config->get('admin_pass')) 
		{
			// If first login attempt, initiate a login attempt counter
			if($loginAttempts == 0) 
			{
				$_POST['loginAttempts'] = 1;
				return FALSE;
			}
			
			// Otherwise, check if attempts are at 3, if so then lock the ASP for now
			else
			{
				if( $loginAttempts >= 3 )
				{
					echo "<blink><p align='center' style=\"font-weight:bold;font-size:170px;color:red;font-family:sans-serif;\">Max Login Attempts Reached</p></blink>";		
					exit;
				}
				else
				{
					$_POST['loginAttempts'] += 1;
					return FALSE;
				}
			}
		}
		
		// Else, the username and password matched, login is a success
		else 
		{
			// Start Session, set session variables
			$_SESSION['adminAuth'] = sha1($this->Config->get('admin_user').':'.$this->Config->get('admin_pass'));
			$_SESSION['adminTime'] = time();
			$SID = session_id();
			return TRUE;
		}
    }
/*
| ---------------------------------------------------------------
| Function: logout()
| ---------------------------------------------------------------
|
| Logs the user out and sets all session variables to Guest.
|
| @Return (None)
|
*/

    public function logout()
    {
        // If sessions is already killed, just return
		if(!$this->check_session()) return;
		
		// Reset Session Values
		$_SESSION['adminAuth'] = '';
		$_SESSION['adminTime'] = '';
		
		// If session exists, unregister all variables that exist and destroy session
		session_destroy();
    }
}
// EOF