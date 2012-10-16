<?php
class Backupdb
{
    public function Init() 
    {
        // Check for post data
        if($_POST['action'] == 'backup')
        {
            $this->Process();
        }
        else
        {
            // Setup the template
            $Template = load_class('Template');
            $Template->render('backupdb');
        }
    }
    
    public function Process()
    {
        // Load the config / Database
        $Config = load_class('Config');
        $Fs = load_class('Filesystem');
        $DB = load_database();
        $tables = getDataTables();
        $errors = array();
        
        // Create Backup Folder
        $backupPath  = str_replace(array('/','\\'), DS, $Config->get('admin_backup_path'));
        
        // Make sure the path is writable before attempting to make the dir
        if( !$Fs->is_writable($backupPath) )
        {
            echo json_encode( 
                array(
                    'success' => false,
                    'message' => 'Database backup path ('. $backupPath .') is *NOT* Writable by the system. Please set the proper permissions to allow the system to create new directories.'
                )
            );
            die();
        }
        
        // Continue making the directory
        $backupPath .= "bak_".date('Ymd_Hi');
        $oldumask = umask(0);
        if( !mkdir($backupPath, 0777) )
        {
            echo json_encode( 
                array(
                    'success' => false,
                    'message' => 'Unable to create new backup path ('. $backupPath .'). Please set the proper permissions to allow the system to create new directories.'
                )
            );
            die();
        }
        umask($oldumask);
        
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
                $backupFile = $backupPath . DS . $DataTable . $Config->get('admin_backup_ext');
                $query = "SELECT * INTO OUTFILE '". addslashes($backupFile) ."' FROM {$DataTable};";
                $result = $DB->query($query)->result();
                if( !$result ) 
                {
                    $errors[] = "Table (" . $DataTable . ") *NOT* Backed Up: ". mysql_error();
                }
            }
        }
        
        // Prepare for Output
        $html = '';
        if( !empty($errors) )
        {
            $html .= 'Failed to backup all database tables... <br /><br />List of Errors:<br /><ul>';
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
                    'message' => 'System Data Backup Successfull! Backup Directory Used: '. $backupPath
                )
            );
        }
    }
}