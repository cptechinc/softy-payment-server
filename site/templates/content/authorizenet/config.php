<?php
	
	define("AUTHORIZENET_API_LOGIN_ID", $user->api_login); 
	define("AUTHORIZENET_TRANSACTION_KEY", $user->api_password); 
	
	if ($user->api_sandbox) {
		define("AUTHORIZENET_SANDBOX", true);
	} else {
		define("AUTHORIZENET_SANDBOX", false);
	}


?>