<?php

	// Set lib directory	
	ini_set("include_path", ini_get("include_path") . PATH_SEPARATOR . "lib" . PATH_SEPARATOR);

	// Read login credentials from config file
	require_once("config.php");

	// Require the API class
	require_once('Wunderlist/api.class.php');
	require_once('Wunderlist/api.files.class.php');
	
	// construct the Wunderlist class using user Wunderlist e-mailaddress and password	
	try
	{
		$wunderlist = new Wunderlist($wlUser, $wlPass);
	}
	catch(Exception $e)
	{
		die( $e->getMessage() );
		// $e->getCode() contains the error code	
	}