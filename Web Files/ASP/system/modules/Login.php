<?php
class Login 
{
	public function Init() 
	{
		$Template = load_class('Template');
		$Template->render('login', false);
	}
}
?>