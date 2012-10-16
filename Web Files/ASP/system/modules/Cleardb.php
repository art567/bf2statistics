<?php
class Cleardb
{
	public function Init() 
	{
		// Check for post data
		if($_POST['action'] == 'clear')
		{
			$this->Process();
		}
		else
		{
			// Setup the template
			$Template = load_class('Template');
			$Template->render('cleardb');
		}
	}
	
	public function Process()
	{
		// Load the config / Database
		$Config = load_class('Config');
		$DB = load_database();
		$tables = getDataTables();
		$errors = array();
		
		// Remove our time limit!
		ini_set('max_execution_time', 0);
		
		// Process each upgrade only if the version is newer
		foreach ($tables as $DataTable) 
		{
			// Check Table Exists
			$query = "SHOW TABLES LIKE '" . $DataTable . "'";
			$result = $DB->query($query);
			if( $DB->num_rows() ) 
			{
				// Table Exists, lets clear it
				$query = "TRUNCATE TABLE `" . $DataTable . "`;";
				$result = $DB->query($query)->result();
				if( !$result ) 
				{
					$errors[] = "Table (" . $DataTable . ") NOT Cleared!". mysql_error();
				}
			}
		}
		
		// Prepare for Output
		$html = '';
		if( !empty($errors) )
		{
			$html .= 'Failed to clear all database tables... <br /><br />List of Errors:<br /><ul>';
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
					'message' => 'System Data Cleared Successfully!'
				)
			);
		}
	}
}