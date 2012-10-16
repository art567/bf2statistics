<?php
class Home
{
	public function Init() 
	{
		$Template = load_class('Template');
		$Template->render('home');
	}
}
?>