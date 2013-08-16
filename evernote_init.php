<?php
	// Import the classes that we're going to be using
	use EDAM\Types\Data, EDAM\Types\Note, EDAM\Types\Resource, EDAM\Types\ResourceAttributes;
	use EDAM\Error\EDAMUserException, EDAM\Error\EDAMErrorCode;
	use Evernote\Client;
	
	ini_set("include_path", ini_get("include_path") . PATH_SEPARATOR . "lib" . PATH_SEPARATOR);

	//Read creditials from external file
	require_once("config.php");
	
	require_once 'Evernote/autoload.php';
	require_once 'Evernote/Evernote/Client.php';
	require_once 'Evernote/packages/Errors/Errors_types.php';
	require_once 'Evernote/packages/Types/Types_types.php';
	require_once 'Evernote/packages/Limits/Limits_constants.php';
	
	// A global exception handler for our program so that error messages all go to the console
	function en_exception_handler($exception)
	{
		echo "Uncaught " . get_class($exception) . ":\n";
		if ($exception instanceof EDAMUserException) {
			echo "Error code: " . EDAMErrorCode::$__names[$exception->errorCode] . "\n";
			echo "Parameter: " . $exception->parameter . "\n";
		} elseif ($exception instanceof EDAMSystemException) {
			echo "Error code: " . EDAMErrorCode::$__names[$exception->errorCode] . "\n";
			echo "Message: " . $exception->message . "\n";
		} else {
			echo $exception;
		}
	}
	set_exception_handler('en_exception_handler');

	// Real applications authenticate with Evernote using OAuth, but for the
	// purpose of exploring the API, you can get a developer token that allows
	// you to access your own Evernote account. To get a developer token, visit
	// https://sandbox.evernote.com/api/DeveloperToken.action
	//$authToken = "your developer token";
	
	// Get authToken from configuration file
	$authToken = $evernote_authToken;
	if ($authToken == "your developer token") {
		print "Please fill in your developer token\n";
		print "To get a developer token, visit https://sandbox.evernote.com/api/DeveloperToken.action\n";
		exit(1);
	}
	// Initial development is performed on our sandbox server. To use the production
	// service, change "sandbox.evernote.com" to "www.evernote.com" and replace your
	// developer token above with a token from
	// https://www.evernote.com/api/DeveloperToken.action
	$client = new Client(array('token' => $authToken));

	$userStore = $client->getUserStore();

	// Connect to the service and check the protocol version
	$versionOK =
		$userStore->checkVersion("Evernote EDAMTest (PHP)",
			 $GLOBALS['EDAM_UserStore_UserStore_CONSTANTS']['EDAM_VERSION_MAJOR'],
			 $GLOBALS['EDAM_UserStore_UserStore_CONSTANTS']['EDAM_VERSION_MINOR']);
	//print "Is my Evernote API version up to date?  " . $versionOK . "\n\n";
	if ($versionOK == 0) {
		exit(1);
	}