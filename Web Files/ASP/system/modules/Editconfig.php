<?php
class Editconfig
{
	public function Init() 
	{
		// Check for post data
		if($_POST['action'] == 'save_config')
		{
			$this->ProcessSave();
		}
		else
		{
			// Load config options
			$Config = load_class('Config');
			
			// Setup the template
			$Template = load_class('Template');
			$Template->set('config', $Config->getAll());
			$Template->render('editconfig');
		}
	}
	
	public function ProcessSave()
	{
		// Load config options
		$Config = load_class('Config');
		
		foreach($_POST as $item => $val) 
		{
			$key = explode('__', $item);
			if($key[0] == 'cfg') 
			{
				$Config->set($key[1], $val);
			}
		}
		
		// Determine if our save is a success
		echo json_encode( array('success' => $Config->save()) );
	}
}
?>