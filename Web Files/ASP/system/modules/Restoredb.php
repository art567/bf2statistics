<?php
class Restoredb
{
	public function Init() 
	{
		// Check for post data
		if($_POST['action'] == 'restore')
		{
			$this->Process();
		}
		else
		{
			// Load the config
			$Config = load_class('Config');

			// Get Existing Backup List
			$baklist = array();
			$dir = dir($Config->get('admin_backup_path'));
			while($file = $dir->read()) 
			{
				if($file != "." && $file != ".." && is_dir($Config->get('admin_backup_path').$file)) 
				{
					$baklist[] = $file;
				}
			}
			sort($baklist);
			$dir->close();
			
			// Build our options list
			$list = array();
			foreach($baklist as $backup)
			{
				$list[] = array('name' => $backup);
			}
	
			// Setup the template
			$Template = load_class('Template');
			$Template->set('options', $list);
			$Template->render('restoredb');
		}
	}
	
	public function Process()
	{
		// Load the config / Database
		$Config = load_class('Config');
		$DB = load_database();
		$tables = getDataTables();
		$errors = array();
		
		// Get our backup path
		$backupPath  = $Config->get('admin_backup_path');
		$backupPath .= $_POST["backupname"];
		
		// Remove our time limit!
		ini_set('max_execution_time', 0);
		
		// Process each upgrade only if the version is newer
		foreach ($tables as $DataTable) 
		{
			// Check Table Exists
			$DB->query("SHOW TABLES LIKE '" . $DataTable . "'");
			if ($DB->num_rows() == 1)
			{
				// Table Exists, lets back it up
				$backupFile = $backupPath ."/". $DataTable . $Config->get('admin_backup_ext');
				if (file_exists($backupFile)) 
				{
					$query = "LOAD DATA INFILE '{$backupFile}' INTO TABLE {$DataTable};";
					$result = $DB->query($query)->result();
					if( !$result ) 
					{
						$errors[] = "Table (" . $DataTable . ") *NOT* Restored: ". mysql_error();
					}
				}
				else
				{
					$errors[] = "Data File (" . $backupFile . ") does *NOT* Exist!!";
				}
			}
		}
		
		// Prepare for Output
		$html = '';
		if( !empty($errors) )
		{
			$html .= 'Failed to restore all database tables... <br /><br />List of Errors:<br /><ul>';
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
					'message' => 'System Data Restored Successfully!'
				)
			);
		}
	}
}