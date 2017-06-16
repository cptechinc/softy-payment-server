<?php 
	$gateway = $page->name; 
	switch($config->servicetype) {
		case 'client':
			$user = $session->forceLogin('rcapsys');
			include ($config->paths->content.$gateway."/client-send-request.php");
			break;
		case 'clientserver':
			$user = $session->forceLogin('rcapsys');
			include ($config->paths->content.$gateway."/$gateway-router.php");
			break;
		case 'server':
			include ($config->paths->content.$gateway."/server-process-request.php");
			break;
	}
	
?>