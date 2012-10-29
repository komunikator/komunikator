<?php

abstract class Controller
{
	static $uris = array();
	
	function handle()
	{
		if (isset($_REQUEST['req']))
		{
			$uris[$_REQUEST['req']];
		} else {
			throw new Exception('Bad request', 400);	
		}
	}
}
?>