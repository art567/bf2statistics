<?php
class Validateranks
{
	public function Init() 
	{
		// Check for post data
		if($_POST['action'] == 'validate')
		{
			$this->Process();
		}
		else
		{
			// Setup the template
			$Template = load_class('Template');
			$Template->render('validateranks');
		}
	}
	
	public function Process()
	{
		// Load the Player class
		$Player = load_class('Player');
		$DB = load_database();
		
		$query = "SELECT `id` FROM player WHERE `score` > 1";
		$pids = $DB->query($query)->fetch_array();
		
		foreach($pids as $pid)
		{
			$Player->validateRank($pid['id']);
		}
        
        // Log the Player messages if any'
        $messages = $Player->messages();
        if(count($messages))
        {
            $err_msg = "Validate Rank Logging Started: ". date('Y-m-d H:i:s') . PHP_EOL;
            foreach($messages as $err)
            {
                $err_msg .= $err . PHP_EOL;
            }
            $err_msg .= PHP_EOL;
            $log = SYSTEM_PATH . DS . 'logs' . DS . 'validate_ranks.log';
            $file = @fopen($log, 'a');
            @fwrite($file, $err_msg);
            @fclose($file);
        }
        
        echo json_encode(array('success' => true));
	}
}
?>