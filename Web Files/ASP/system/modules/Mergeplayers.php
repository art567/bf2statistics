<?php
class Mergeplayers
{
    public function Init() 
    {
        // Check for post data
        if($_POST['action'] == 'merge')
        {
            $this->Process();
        }
        else
        {
            // Setup the template
            $Template = load_class('Template');
            $Template->render('mergeplayers');
        }
    }
    
    public function Process()
    {
        // Make Sure Script doesn't timeout
        set_time_limit(0);

        // Load the database and player class
        $DB = load_database();
        $Player = load_class('Player');
        
        // Get PlayerID's
        $pids = array();
        $pids[0] = (isset($_POST['target_pid'])) ? mysql_real_escape_string($_POST['target_pid']) : 0; // Target PID
        $pids[1] = (isset($_POST['source_pid'])) ? mysql_real_escape_string($_POST['source_pid']) : 0; // Source PID
        
        // Make sure the PID's dont match!
        if($pids[0] == $pids[1]) 
        {
            echo json_encode( array('success' => false, 'message' => 'Target player cannot be the same player as the Source player!') );
            die();
        }
        
        // Check Players Exist
        foreach ($pids as $key => $pid) 
        {
            // Make sure PID's are valid
            if (!is_numeric($pid) || $pid == 0) 
            {
                echo json_encode( array('success' => false, 'message' => 'Invalid player ID\'s entered.') );
                die();
            }
            
            // Retrieve our player
            $query = "SELECT `id` FROM `player` WHERE id = {$pid}";
            if ($DB->query($query)->num_rows() == 1)
            {
                $pids_exist = true;
            } 
            else 
            {
                echo json_encode( array('success' => false, 'message' => "PID ({$pid}) is not a valid player!") );
                die();
            }
        }
        
        // Create our log array
        $log = array();
        $log[] = "Merging Player $pid[1] into Player $pid[0] ...";
        
        // Merge Single-line data tables
        $DataTables = array('army','kits','vehicles','weapons','player');
        foreach ($DataTables as $DataTable) 
        {
            $log[] =  " -> Merging {$DataTable} table...";
            $query = "SELECT * FROM {$DataTable} WHERE id = {$pids[1]}";
            $result = $DB->query($query)->result();
            if ($DB->num_rows() == 1)
            {
                $fieldCount = mysql_num_fields($result);
                $row = mysql_fetch_row($result);
                
                // Build Update Query
                $query = "UPDATE {$DataTable} SET ";
                for( $i = 1; $i < $fieldCount; $i++ ) 
                {
                    if (mysql_field_type($result, $i) == 'int') 
                    {
                        if($DataTable == 'player' && mysql_field_name($result, $i) == 'rank')
                        {
                            $query .= "`" . mysql_field_name($result, $i) . "` = 0,\n";
                        }
                        elseif ($DataTable == 'player' && mysql_field_name($result, $i) == 'joined') 
                        {
                            $query .= "`" . mysql_field_name($result, $i) . "` = " . $row[$i] . ",\n";
                        } 
                        elseif ($DataTable == 'player' &&  mysql_field_name($result, $i) == 'lastonline') 
                        {
                            $query .= "`" . mysql_field_name($result, $i) . "` = `" . mysql_field_name($result, $i) . "`,\n";
                        } 
                        elseif ($DataTable == 'player' &&  mysql_field_name($result, $i) == 'rndscore') 
                        {
                            $query .= "`" . mysql_field_name($result, $i) . "` = (SELECT IF(" . $row[$i] . " > `" . mysql_field_name($result, $i) . "`, " . $row[$i] . ", `" . mysql_field_name($result, $i) . "`)),\n";
                        } 
                        else 
                        {
                            $query .= "`" . mysql_field_name($result, $i) . "` = `" . mysql_field_name($result, $i) . "` + " . $row[$i] . ",\n";
                        }
                    }
                }
                $query = rtrim($query, ",\n") . "\nWHERE id = {$pids[0]};";
                
                // Update Data
                if ($DB->query($query)->result()) 
                {
                    $log[] =  "\t\tSuccess!";
                    
                    // Remove Old Data
                    $query = "DELETE FROM `{$DataTable}` WHERE id = {$pids[1]};";
                    if ($DB->query($query)->result()) 
                    {
                        $log[] =  " -> Old Player Data ({$DataTable}) Removed.";
                    } 
                    else 
                    {
                        $log[] =  "\t\tFailed to Remove Old Player Data from ({$DataTable})! ". mysql_error();
                    }
                } 
                else 
                {
                    $log[] =  "\t\t".mysql_error();
                    echo json_encode( array('success' => false, 'message' => "Fetal Error while merging players: <br /><br />Mysql Error: ". mysql_error()) );
                    die();
                }
            }
        }
        
        // Reset Unlocks
        $log[] =  " -> Reseting Unlocks for Player ({$pids[0]})...";
        $query = "UPDATE unlocks SET state = 'n' WHERE (id = {$pids[0]})";
        if ($DB->query($query)->result()) 
        {
            $query = "UPDATE player SET availunlocks = 0, usedunlocks = 0 WHERE id = {$pids[0]}";
            if ($DB->query($query)->result()) 
            {
                $log[] =  "\t\tSuccess!";
            } 
            else 
            {
                $log[] =  "\t\tReset Unlocks Failed! ".mysql_error();
            }
            
            // Remove Old Unlocks Data
            $log[] =  " -> Removing Old Unlocks for Player ({$pids[1]})...";
            $query = "DELETE FROM unlocks WHERE (id = {$pids[1]})";
            if ($DB->query($query)->result()) 
            {
                $log[] =  "\t\tSuccess!";
            } 
            else 
            {
                $log[] =  "\t\tUnlocks Removal Failed! ".mysql_error();
            }
        } 
        else 
        {
            $log[] =  "\t\tFailed! ".mysql_error();
            echo json_encode( array('success' => false, 'message' => "Fetal Error While Resetting Unlocks: <br /><br />Mysql Error: ". mysql_error()) );
            die();
        }
        
        // Merge Awards Data
        $log[] =  " -> Merging Awards table...";
        $query = "SELECT * FROM awards WHERE id = {$pids[1]};";
        $result = $DB->query($query)->result();
        if( $DB->num_rows() )	
        {
            $awards = $DB->fetch_array();
            foreach($awards as $rowsrc)
            {
                // Check Awards exist
                if ($rowsrc['awd']) 
                {
                    $query = "SELECT * FROM awards WHERE id = {$pids[0]} AND awd = " . $rowsrc['awd'] . ";";
                    $chkresult = $DB->query($query)->result();
                    if( $DB->num_rows() ) 
                    {
                        // Update Award
                        $rowdest = mysql_fetch_array( $chkresult );
                        $query = "UPDATE `awards` SET\n";

                        switch ($rowsrc['awd'])
                        {
                            case 2051902:	// Gold
                            case 2051907:	// Silver
                            case 2051919:	// Bronze
                                $query .= "`level` = `level` + " . $rowsrc['level'] . ",\n";
                                break;
                            default:
                                $query .= "level = " . MAX($rowsrc['level'],$rowdest['level']) . ",\n";
                        }

                        $query .= "earned = " . MIN($rowsrc['earned'],$rowdest['earned']) . ",\n";
                        $query .= "first = " . MIN($rowsrc['first'],$rowdest['first']) . "\n";
                        $query .= "WHERE id = {$pids[0]} AND `awd` = " . $rowsrc['awd'] . ";";
                        if ($DB->query($query)->result())
                        {
                            $log[] =  "\t\tAward {$rowsrc['awd']} Update Success!";
                        } 
                        else 
                        {
                            $log[] =  "\t\tAward {$rowsrc['awd']} Update Failed: ".mysql_error();
                        }
                    } 
                    else 
                    {
                        // Insert Award
                        $query  = "INSERT INTO `awards` SET\n";
                        $query .= "`id` = {$pids[0]},\n";
                        $query .= "`awd` = " . $rowsrc['awd'] . ",\n";
                        $query .= "`level` = " . $rowsrc['level'] . ",\n";
                        $query .= "`earned` = " . $rowsrc['earned'] . ",\n";
                        $query .= "`first` = " . $rowsrc['first'] . ";";
                        if ($DB->query($query)->result()) 
                        {
                            $log[] =  "\t\tAward {$rowsrc['awd']} Insert Success!";
                        } 
                        else 
                        {
                            $log[] =  "\t\tAward {$rowsrc['awd']} Insert Failed: ".mysql_error();
                        }
                    }
                }
            }
            $log[] =  "\t\tAwards Table Merged!";
            
            // Remove Old Awards Data
            $log[] =  " -> Removing Old Awards for Player ({$pids[1]})...";
            $query = "DELETE FROM awards WHERE (id = {$pids[1]})";
            if ($DB->query($query)->result()) 
            {
                $log[] =  "\t\tSuccess!";
            } 
            else 
            {
                $log[] =  "\t\tFailed! ".mysql_error();
            }
        }
        
        // Merge Maps Data
        $log[] =  " -> Merging Maps table...";
        $query = "SELECT * FROM `maps` WHERE `id` = {$pids[1]};";
        $result = $DB->query($query)->result();
        if( $DB->num_rows() )	
        {
            $mapdata = $DB->fetch_array();
            foreach($mapdata as $rowsrc)
            {
                // Check Map exist
                if ($rowsrc['mapid'] >= 0) 
                {
                    $query = "SELECT * FROM `maps` WHERE `id`= {$pids[0]} AND `mapid` = " . $rowsrc['mapid'] . ";";
                    $chkresult = $DB->query($query)->result();
                    if( $DB->num_rows() )
                    {
                        // Update Map Data
                        $rowdest = mysql_fetch_array( $chkresult );
                        $query = "UPDATE `maps` SET";
                        $query .= " `time` = `time` + " . $rowsrc['time'] . ",";
                        $query .= " `win` = `win` + " . $rowsrc['win'] . ",";
                        $query .= " `loss` = `loss` + " . $rowsrc['loss'] . ",";
                        if ($rowsrc['best'] > $rowdest['best']) 
                        {
                            $query .= " `best` = " . $rowsrc['best'] . ",";
                        }
                        if ($rowsrc['worst'] < $rowdest['worst']) 
                        {
                            $query .= " `worst` = `worst` + " . $rowsrc['worst'];
                        }
                        
                        // Trim the last comma if there is one
                        $query = trim($query, ',');
                        $query .= " WHERE `id` = '{$pids[0]}' AND `mapid` = '" . $rowsrc['mapid'] . "';";
                        if ($DB->query($query)->result()) 
                        {
                            $log[] =  "\t\tMap {$rowsrc['mapid']} Update Success!";
                        } 
                        else 
                        {
                            $log[] =  "\t\tMap {$rowsrc['mapid']} Update Failed: ".mysql_error();
                        }
                    } 
                    else 
                    {
                        // Insert Map Data
                        $query  = "INSERT INTO `maps` SET\n";
                        $query .= "`id` = {$pids[0]},\n";
                        $query .= "`mapid` = " . $rowsrc['mapid'] . ",\n";
                        $query .= "`time` = " . $rowsrc['time'] . ",\n";
                        $query .= "`win` = " . $rowsrc['win'] . ",\n";
                        $query .= "`loss` = " . $rowsrc['loss'] . ",\n";
                        $query .= "`best` = " . $rowsrc['best'] . ",\n";
                        $query .= "`worst` = " . $rowsrc['worst'] . ";";
                        if ($DB->query($query)->result()) 
                        {
                            $log[] =  "\t\tMap {$rowsrc['mapid']} Insert Success!";
                        } 
                        else 
                        {
                            $log[] =  "\t\tMap {$rowsrc['mapid']} Insert Failed: ".mysql_error();
                        }
                    }
                } 
                else 
                {
                    $log[] =  "\t\tMapID Invalid!";
                }
            }
            $log[] =  "\t\tDone!";
            
            // Remove Old Maps Data
            $log[] =  " -> Removing Old Maps for Player ({$pids[1]})...";
            $query = "DELETE FROM maps WHERE (id = {$pids[1]})";
            if ($DB->query($query)->result()) 
            {
                $log[] =  "\t\tSuccess!";
            } 
            else 
            {
                $log[] =  "\t\tFailed! : ".mysql_error();
            }
        }
        
        // Update Kills Data
        $log[] =  " -> Updating Kills Data...";
        $query = "SELECT * FROM kills WHERE attacker = {$pids[1]};";
        $result = $DB->query($query)->result();
        if( $DB->num_rows() )	
        {
            $killdata = $DB->fetch_array();
            foreach($killdata as $rowsrc) 
            {
                // Check Kills exist
                if ($rowsrc['victim']) 
                {
                    $query = "SELECT * FROM kills WHERE attacker = {$pids[0]} AND victim = " . $rowsrc['victim'] . ";";
                    $chkresult = $DB->query($query)->result();
                    if( $DB->num_rows() ) 
                    {
                        // Update Existing record
                        $query = "UPDATE `kills` SET\n";
                        $query .= "`count` = `count` + " . $rowsrc['count'] . "\n";
                        $query .= "WHERE attacker = {$pids[0]} AND victim = " . $rowsrc['victim'] . ";";
                        if ($DB->query($query)->result()) 
                        {
                            // Success
                        } 
                        else 
                        {
                            $log[] =  "\t\tERROR: Kills data not updated: ".mysql_error();
                        }
                    } 
                    else 
                    {
                        // Insert Kills
                        $query  = "INSERT INTO `kills` SET\n";
                        $query .= "attacker = {$pids[0]},\n";
                        $query .= "victim = " . $rowsrc['victim'] . ",\n";
                        $query .= "`count` = " . $rowsrc['count'] . ";";
                        if ($DB->query($query)->result()) 
                        {
                            // Success
                        } 
                        else 
                        {
                            $log[] =  "\t\tERROR:Kills data not inserted: ".mysql_error();
                        }
                    }
                }
            }
            $log[] =  "\t\tKills Done!";
            
            // Remove Old Kills Data
            $log[] =  " -> Removing Old Kills for Player ({$pids[1]})...";
            $query = "DELETE FROM kills WHERE (attacker = {$pids[1]})";
            if ($DB->query($query)->result()) 
            {
                $log[] =  "\t\tSuccess!";
            } 
            else 
            {
                $log[] =  "\t\tFailed! : ".mysql_error();
            }
        }
        
        // Update Deaths Data
        $log[] =  " -> Updating Deaths Data...";
        $query = "SELECT * FROM kills WHERE victim = {$pids[1]};";
        $result = $DB->query($query)->result();
        if( $DB->num_rows() )	
        {
            $deathdata = $DB->fetch_array();
            foreach($deathdata as $rowsrc) 
            {
                // Check Deaths exist
                if ($rowsrc['attacker']) 
                {
                    $query = "SELECT * FROM kills WHERE attacker = " . $rowsrc['attacker'] . " AND victim = {$pids[0]};;";
                    $chkresult = $DB->query($query)->result();
                    if( $DB->num_rows() ) 
                    {
                        // Update Existing record
                        $query = "UPDATE `kills` SET\n";
                        $query .= "`count` = `count` + " . $rowsrc['count'] . "\n";
                        $query .= "WHERE attacker = " . $rowsrc['attacker'] . " AND victim = {$pids[0]};";
                        if ($DB->query($query)->result()) 
                        {
                            // Success
                        } 
                        else 
                        {
                            $log[] =  "\t\tERROR: Kills data not updated: ".mysql_error();
                        }
                    } 
                    else 
                    {
                        // Insert Deaths
                        $query  = "INSERT INTO `kills` SET\n";
                        $query .= "attacker = " . $rowsrc['attacker'] . ",\n";
                        $query .= "victim = {$pids[0]},\n";
                        $query .= "`count` = " . $rowsrc['count'] . ";";
                        if ($DB->query($query)->result()) 
                        {
                            // Success
                        } 
                        else 
                        {
                            $log[] =  "\t\tERROR: Kills data not inserted: ".mysql_error();
                        }
                    }
                }
            }
            $log[] =  "\t\tDeaths Done!";
            
            // Remove Old Deaths Data
            $log[] =  " -> Removing Old Deaths for Player ({$pids[1]})...";
            $query = "DELETE FROM kills WHERE (victim = {$pids[1]})";
            if ($DB->query($query)->result()) 
            {
                $log[] =  "\t\tSuccess!";
            } 
            else 
            {
                $log[] =  "\t\tFailed! :  ".mysql_error();
            }
        }

        $log[] =  "Done! :)\n";
        
        // Validate rank
        $Player->validateRank($pids[0]);
        
        // Delete the old player
        if( !$Player->deletePlayer($pids[1]) )
        {
            echo json_encode( 
                array(
                    'success' => true,
                    'type' => 'warning',
                    'message' => "Failed to delete source Player ($pids[1]). You will need to manually delete this player from the \"Manage Players\" Menu. "
                ) 
            );
        }
        else
        {
            // Success
            echo json_encode( 
                array(
                    'success' => true,
                    'type' => 'success',
                    'message' => "Player ($pids[1]) -> Merged to -> Player ($pids[0]) Successfully!"
                ) 
            );
        }
        
        // Log the messages
        $lines = "Merge Players Logging Started: ". date('Y-m-d H:i:s') . PHP_EOL;
        foreach($log as $line)
        {
            $lines .= $line . PHP_EOL;
        }
        $lines .= PHP_EOL;
        $log = SYSTEM_PATH . DS . 'logs' . DS . 'merge_payers.log';
        $file = @fopen($log, 'a');
        @fwrite($file, $lines);
        @fclose($file);
    }
}
?>