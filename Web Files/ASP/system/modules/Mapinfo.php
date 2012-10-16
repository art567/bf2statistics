<?php
class Mapinfo
{
	public function Init() 
	{
		// Get array
		$this->DB = load_database();
		$result = $this->DB->query("SELECT * FROM mapinfo ORDER BY id ASC;")->fetch_array();
		if($result == false) $result = array();
		
		// Set proper time format
		foreach($result as $key => $value)
		{
			// Set formated time
			$result[$key]['time'] = format_time($value['time']);
			
			// Format custom map text
			$result[$key]['custom'] = ($value['custom'] == true) ? "<font color='red'>YES</font>" : "<font color='green'>NO</font>";
		}
	
		$Template = load_class('Template');
		$Template->set('maps', $result);
		$Template->render('mapinfo');
	}
}
?>