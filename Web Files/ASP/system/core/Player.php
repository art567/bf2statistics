<?php
/* 
| --------------------------------------------------------------
| BF2 Statistics Admin Util
| --------------------------------------------------------------
| Author:       Steven Wilson 
| Copyright:    Copyright (c) 2012
| License:      GNU GPL v3
| ---------------------------------------------------------------
| Class: Player()
| ---------------------------------------------------------------
|
*/
class Player
{
    /* Class Variables */
    protected $DB;
    protected $messages = array();
    protected $rankdata = false;
    protected $awardsdata = false;
    
/*
| ---------------------------------------------------------------
| Constructer
| ---------------------------------------------------------------
*/ 
    public function __construct()
    {
        // Init DB connection
        $this->DB = load_database();
        
        // Load Rank data
        if(!$this->rankdata)
        {
            require( SYSTEM_PATH . DS . 'data'. DS . 'ranks.php' );
            $this->rankdata = $ranks;
        }
        
        // Import Backend Awards Data
        if(!$this->awardsdata)
        {
            require_once( SYSTEM_PATH . DS . 'data' . DS . 'awards.php' );
            $this->awardsdata = buildBackendAwardsData('xpack');
        }
    }
    
/*
| ---------------------------------------------------------------
| Method: log()
| ---------------------------------------------------------------
|
| This method logs messages from the methods in this class
|
*/ 
    protected function log($message)
    {
        $this->messages[] = $message;
    }
    
/*
| ---------------------------------------------------------------
| Method: messages()
| ---------------------------------------------------------------
|
| This method returns all the logged messages
|
*/ 
    public function messages()
    {
        return $this->messages;
    }
  
/*
| ---------------------------------------------------------------
| Method: deletePlayer()
| ---------------------------------------------------------------
|
| This method is used to delete all player data from all bf2 tables
|
*/   
    public function deletePlayer($pid)
    {
        // Build Data Table Array
        $return = true;
        $DataTables = getDataTables();
        foreach ($DataTables as $DataTable) 
        {
            // Check Table Exists
            $this->DB->query("SHOW TABLES LIKE '" . $DataTable . "'");
            if ($this->DB->num_rows() == 1) 
            {
                // Table Exists, lets clear it
                $query = "DELETE FROM `" . $DataTable . "` ";
                if ($DataTable == 'kills') 
                {
                    $query .= "WHERE `attacker` = {$pid} OR `victim` = {$pid};";
                } 
                else 
                {
                    $query .= "WHERE `id` = {$pid};";
                }

                $result = $this->DB->query($query)->result();
                if ($result) 
                {
                    $this->log("Player removed from Table (" . $DataTable . ").");
                } 
                else 
                {
                    $return = false;
                    $this->log("Player *NOT* removed from Table (" . $DataTable . ")!");
                }
            }
        }
        
        return $return;
    }

/*
| ---------------------------------------------------------------
| Method: validateRank()
| ---------------------------------------------------------------
|
| This method will validate and correct the given players rank
| based on the values stored in the "system/data/ranks.php"
|
*/    
    public function validateRank($pid)
    {
        // Get our player
        $query = "SELECT `id`, `score`, `rank` FROM `player` WHERE `id`=". mysql_real_escape_string($pid);
        $this->DB->query($query);
        if($this->DB->num_rows())
        {
            // Setup our player variables
            $row   = $this->DB->fetch_row();
            $pid   = (int)$row['id'];
            $score = (int)$row['score'];
            $rank  = (int)$row['rank'];
            
            // Our set rank and expected ranks
            $setRank = 0;
            $expRank = 0;
            
            // Figure out which rank we are suppossed to be by points
            foreach($this->rankdata as $key => $value)
            {
                // Keep going till we are no longer in the correct point range
                if($value['points'] != -1 && ($value['points'] < $score))
                {
                    $expRank = $key;
                }
            }
            
            // SetRank if we are good!
            if($rank == $expRank)
            {
                $setRank = $rank;
            }

            // If the rank isnt as expected, and we are not a 4 start gen... then we need to process ranks
            elseif($rank != $expRank && $rank != 21)
            {
                // Get player awards
                $query = "SELECT * FROM `awards` WHERE `id` = ". mysql_real_escape_string($pid);
                $awards = $this->DB->query($query)->fetch_array();
                if($awards == false) $awards = array();
                
                // Build our player awards list
                $player_awards = array();
                foreach($awards as $value)
                {
                    $player_awards[$value['awd']] = $value['level'];
                }
                
                // Prevent rank skipping unless the player meets ALL prior rank requirements
                for($i = 1; $i <= $expRank; $i++)
                {
                    /// Process Has Rank ///

                    // First, we must check to see if the set rank is IN the net rank Reqs.
                    $reqRank = $this->rankdata[$i]['has_rank'];
                    if(is_array($reqRank))
                    {
                        if(!in_array($setRank, $reqRank))
                        {
                            // Check to see if the current ranks points match
                            if($this->rankdata[$i]['points'] != $this->rankdata[$setRank]['points'])
                            {
                                continue;
                            }
                        }
                    }
                    elseif( $setRank != $reqRank)
                    {
                        // Check to see if the current ranks points match, if the do
                        // We can continue, otherwise we cant go any higher
                        if($this->rankdata[$i]['points'] != $this->rankdata[$setRank]['points'])
                        {
                            continue;
                        }
                    }   
                    
                    /// Process Has Awards ///
                    
                    // If rank requires medals, then we have to check if the player has them
                    if(!empty($this->rankdata[$i]['has_awards']))
                    {
                        // Good marker
                        $good = true;

                        // Make sure the player has each reward required
                        foreach($this->rankdata[$i]['has_awards'] as $award => $level)
                        {
                            // Check if the award is in the list of players earned awards
                            if(array_key_exists($award, $player_awards))
                            {
                                // Check to see if the level of the earned award is geater or equalvalue of the required award
                                if($player_awards[$award] >= $level)
                                {
                                    // The award is good, move to the next award in the loop
                                    continue;
                                }
                                else
                                {
                                    // Award level is too low
                                    $good = false;
                                }
                            }
                            else
                            {
                                // The user doesnt have the award
                                $good = false;
                            }
                        }
                        
                        // If we have the req. medals for this rank
                        if($good == true)
                        {
                            $setRank = $i;
                        }
                    }
                    else
                    {
                        $setRank = $i;
                    }
                }
                
                // Done :)
            }
            
            // Update Database if we arent a 4 star gen, or smoc with a higher rank award
            if(($rank == 11 && $setRank > 11) || ($rank != 21 && $rank != $setRank))
            {
                // Log
                $this->log(
                    " -> Rank Correction (".$row['id']."):". PHP_EOL
                    ."\tScore: ". $score . PHP_EOL
                    ."\tExpected: ". $expRank . PHP_EOL
                    ."\tFound: ".$rank . PHP_EOL
                    ."\tNew Rank: ".$setRank
                );
                
                // Query the update
                $query = "UPDATE `player` SET `rank` = ". $setRank ." WHERE `id` = ". $pid;
                if (!$this->DB->query($query)->result()) 
                {
                    return FALSE;
                }
            }
            
            // Return Success
            return TRUE;
        }
        else 
        {
            return FALSE;
        }
    }

/*
| ---------------------------------------------------------------
| Method: checkBackendAwards()
| ---------------------------------------------------------------
|
| This method will validate and correct the given players 'army'
| awards based on the values stored in the "system/data/awards.php"
|
*/  
    public function checkBackendAwards($pid)
    {
        // Where clause Substitution String
        $awards_substr = "###";
        $awards = $this->awardsdata;
        $pid = mysql_real_escape_string($pid);
        
        $query = "SELECT `id` FROM `player` WHERE `id` = ". $pid;
        $result = $this->DB->query( $query )->result();
        if(!$result) return false;
        
        foreach ($awards as $award) 
        {
            // Check if Player already has Award
            $query = "SELECT `awd`, `level` FROM `awards` WHERE (id = " . $pid . ") AND (awd = {$award[0]}) LIMIT 1";
            $awardrows = $this->DB->query( $query )->num_rows();
            
            if ($awardrows > 0) $rowawd = $this->DB->fetch_row();

            // Loop through each award, and check the criteria
            $chkcriteria = false;
            foreach ($award[3] as $criteria) 
            {
                // Recieveing ribbon awards multiple times is NOT supported
                if ($award[2] == 2) 
                {
                    // Can receive multiple times
                    if ($awardrows > 0) 
                    {
                        $Medal_Next = true;
                        $where = str_replace($awards_substr, $rowawd['level'] + 1, $criteria[3]);
                    } 
                    else 
                    {
                        $Medal_Next = false;
                        $where = str_replace($awards_substr, 1, $criteria[3]);
                    }
                } 
                else 
                {
                    $where = $criteria[3];
                }
                
                // Check to see if the player meets the requirments for the award
                $query = "SELECT {$criteria[1]} AS checkval FROM {$criteria[0]} WHERE (id = " . $pid . ") AND ({$where}) ORDER BY id;";
                $this->DB->query( $query )->result();
                if ($this->DB->num_rows() > 0) 
                {
                    $rowchk = $this->DB->fetch_row();
                    if ($rowchk['checkval'] >= $criteria[2]) 
                    {
                        $chkcriteria = true;
                    } 
                    else 
                    {
                        $chkcriteria = false;
                        break;
                    }
                }
            }
            
            // If player is meets the requirements, but hasnt been awarded the reward...
            if ($chkcriteria && $awardrows == 0) 
            {
                // Insert information
                $this->log("Award Missing ({$award[0]}) for Player ({$pid}). Adding award to Players Awards...");
                $query = "INSERT INTO awards SET
                    id = " . $pid . ",
                    awd = {$award[0]},
                    level = 1,
                    earned = " . time() . ",
                    first = 0;";
                $this->DB->query( $query ); 
            }
            
            // Else, if Player has award but doesnt meet requirements
            elseif (!$chkcriteria && $awardrows > 0) 
            {
                // Delete information
                $this->log("Player ({$pid}) Has Award ({$award[0]}), but does not meet requirements! Removing award...");
                $query = "DELETE FROM awards WHERE (id = " . $pid . " AND awd = {$award[0]});";
                $this->DB->query( $query );
            }
            
            // Maybe additional award for medal?
            elseif($award[2] == 2 &&  $Medal_Next == true && $chkcriteria == true)
            {
                // Update Award
                $this->log("Award Missing ({$award[0]}) for Player ({$pid}). Adding award to Players Awards...");
                $query = "UPDATE awards SET level = ". ($rowawd['level'] + 1) .", earned = " . time() . " WHERE id = " . $pid . " AND awd = {$award[0]}";
                $this->DB->query( $query ); 
            }
        }

        return TRUE;
    }
}