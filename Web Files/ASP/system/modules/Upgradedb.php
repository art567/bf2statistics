<?php
class Upgradedb
{
	public function Init() 
	{
		// Check for post data
		if($_POST['action'] == 'upgrade')
		{
			$this->Process();
		}
		else
		{
			// Db Version Compare
			$Config = load_class('Config');
			if(verCmp( getDbVer() ) < verCmp( $Config->get('db_expected_ver') ))
			{
				$button = 'Run Updates';
				$disabled = '';
			}
			else
			{
				$button = 'System Up To Date';
				$disabled = 'disabled="disabled"';
			}
		
			// Setup the template
			$Template = load_class('Template');
			$Template->set('button_text', $button);
			$Template->set('disabled', $disabled);
			$Template->render('upgradedb');
		}
	}
	
	public function Process()
	{
		// Load the config / Database
		$Config = load_class('Config');
		$DB = load_database();
		$errors = array();
		
		// Remove our time limit!
		ini_set('max_execution_time', 0);
		
		// Get DB Version
		$curdbver = verCmp(getDbVer());
		
		// Import Upgrade Schema/Data
		require( SYSTEM_PATH . DS . 'database'. DS .'sql.dbupgrade.php' );
		
		// Process each upgrade only if the version is newer
		foreach ($sqlupgrade as $query) 
		{
			if ($curdbver < verCmp($query[1])) 
			{
				if ( !$DB->query($query[2])->result() ) 
				{
					$errors[] = $query[0]." *FAILED*: ". mysql_error();
				}
			} 
		}
		
		// Prepare for Output
		$html = '';
		if( !empty($errors) )
		{
			$html .= 'Upgrade failed to install all the neccessary database data...<br /><br />List of Errors:<br /><ul>';
			foreach($errors as $e)
			{
				$html .= '<li>'. $e .'</li>';
			}
			$html .= '</ul>';
			
			echo json_encode( 
				array(
					'success' => false,
					'message' => $html
				)
			);
		}
		else
		{
			echo json_encode( 
				array(
					'success' => true,
					'message' => 'System Upgraded Successfully!'
				)
			);
		}
	}
}