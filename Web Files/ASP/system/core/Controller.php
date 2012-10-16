<?php
/* 
| --------------------------------------------------------------
| BF2 Statistics Admin Util
| --------------------------------------------------------------
| Author:       Steven Wilson
| Copyright:    Copyright (c) 2012
| License:      GNU GPL v3
| ---------------------------------------------------------------
| Class: Controller()
| ---------------------------------------------------------------
|
*/

class Controller 
{
	protected $Auth, $Config;

	public function Init() 
	{
		// Load our Auth and Config classes
		$this->Auth = load_class('Auth');
		$this->Config = load_class('Config');
		
		// Define our database version!
		define('DB_VER', getDbVer());
		
		// First, Lets make sure the IP can view the ASP
		if(!checkIpAuth( $this->Config->get('admin_hosts') )) 
		{
			die("<font color='red'>ERROR:</font> You are NOT Authorised to access this Page! (Ip: ". $_SERVER['REMOTE_ADDR'] .")");
		}
		
		// Always set a post and get actions
		if(!isset($_POST['action'])) $_POST['action'] = null;
		if(!isset($_GET['action']))  $_GET['action'] = null;
		
		// Get / Set our current task
		$task = (isset($_GET['task'])) ? $_GET['task'] : false;
		if($task == false)
		{
			(isset($_POST['task'])) ? $_GET['task'] = $_POST['task'] : $_GET['task'] = 'home';
		}

		// Check for login / logout requests
		if($_POST['action'] == 'login') 
		{
			$this->Auth->login($_POST['username'], $_POST['password']);
		}
		elseif($_POST['action'] == 'logout' || $_GET['action'] == 'logout') 
		{
			$this->Auth->logout();
		}

		// Check and see if the user is logged in
		if( !$this->Auth->check_session() )
		{
			include( SYSTEM_PATH . DS . 'modules' . DS . 'Login.php' );
			$Module = new Login();
			$Module->Init();
		}
		else
		{
			$this->loadTask();
		}
	}
	
	protected function loadTask() 
	{
		// Uppercase the classname
		$task = ucfirst( strtolower($_GET['task']) );
		
		// Process the task by making sure the module exists
		$file = SYSTEM_PATH . DS . 'modules' . DS . $task . '.php';
		if( !file_exists($file) )
		{
			// 404
			$Template = load_class('Template');
			$Template->render('404');
			return;
		}
		
		// Load the module and run!
		include( $file );
		$Module = new $task();
		$Module->Init();
	}
}
?>