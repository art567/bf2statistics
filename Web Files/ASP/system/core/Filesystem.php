<?php
class Filesystem
{
	public function is_writable($path) 
	{
		// Check if a directoy path was given
		if($path[strlen($path)-1] == '/')
		{
			return $this->is_writable($path . uniqid(mt_rand()) .'.tmp');
		}
		elseif(is_dir($path))
		{
			return $this->is_writable($path .'/'. uniqid(mt_rand()) .'.tmp');
		}
		
		// check tmp file for read/write capabilities
		$exists = file_exists($path);
		$handle = @fopen($path, 'a');
		if ($handle === false) return false;
		fclose($handle);
		if (!$exists) unlink($path);
		return true;
	}
}