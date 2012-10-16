<?php
class Installdb
{
	public function Init() 
	{
		// Check for post data
		if($_POST['action'] == 'install')
		{
			$this->Install();
		}
		else
		{
			// Setup the template
			$Template = load_class('Template');
			$Template->render('installdb');
		}
	}
	
	public function Install()
	{
		// Load the config / Database
		$Config = load_class('Config');
		$errors = array();
		
		// Remove our time limit! Ip2Nation can take awhile
		ini_set('max_execution_time', 0);
		
		// Store New/Changed config items
		foreach ($_POST as $item => $val) 
		{
			$key = explode('__', $item);
			if ($key[0] == 'cfg') 
			{
				$Config->set($key[1],$val);
			}
		}
		$Config->Save();
		
		// Load the database
		$DB = load_database();
		if($DB->status() !== 1)
		{
			switch( $DB->status() )
			{
				case 0:
					echo json_encode( 
						array(
							'success' => false, 
							'errors' => false,
							'message' => 'Failed to establish connection to ('. $Config->get('db_host') .'). Please make sure that the mysql database is online, and the data entered is correct.'
						)
					);
					break;
				case -1:
					echo json_encode( 
						array(
							'success' => false, 
							'errors' => false,
							'message' => 'Failed to select database ('. $Config->get('db_name') .') Please make sure the database exists'
						)
					);
					break;
			}
			die();
		}
		
		// Import Schema and Default data
		require( SYSTEM_PATH . DS . 'database'. DS .'sql.dbschema.php' );
		require( SYSTEM_PATH . DS . 'database'. DS .'sql.dbdata.php' );
		
		// Process Schema
		foreach ($sqlschema as $query) 
		{
			if( !$DB->query($query[1], true)->result() ) 
			{
				$errors[] = $query[0]." *NOT* Installed: ". mysql_error();
			} 
		}
		
		// Process Defaut Data
		$i = 0;
		foreach ($sqldata as $query) 
		{
			if( !$DB->query($query[1], true)->result() ) 
			{
				$errors[] = $query[0]." *NOT* Installed: ". mysql_error();
			}
		}
		
		// Prepare for Output
		$html = '';
		if( !empty($errors) )
		{
			$html .= 'Installation failed to install all the neccessary database data...<br /><br />List of Errors:<br /><ul>';
			foreach($errors as $e)
			{
				$html .= '<li>'. $e .'</li>';
			}
			$html .= '</ul>';
			
			echo json_encode( 
				array(
					'success' => false,
					'errors' => true,
					'message' => $html
				)
			);
		}
		else
		{
			echo json_encode( 
				array(
					'success' => true,
					'errors' => false,
					'message' => 'System Installed Successfully! <a href="?task=testconfig">Click here to go to the System Test screen</a> to make sure everything is in working order.'
				)
			);
		}
	}
}