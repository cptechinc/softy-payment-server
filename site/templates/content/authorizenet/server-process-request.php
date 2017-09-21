<?php 
	header('Content-Type: application/json');
	$gateway = $page->name; 
	$ordn = $input->get->text('ordn');
	
	if ($user->isLoggedin()) {
		
	} elseif ($input->post->login && $input->post->password) {
		$username = $input->post->username('login');
	    $pass = $input->post->password; 
		
	    $user = $session->login($username, $pass);
		
	    if ($user) {
			
	    } else {
	    	$response = array(
				'error' => true,
				'message' => 'You payment server login and password are incorrect'
			);
	    }
	} else {
		$response = array(
			'error' => true,
			'message' => 'You are not logged in to the payment server'
		);
	}

	if ($user) {
		if ($input->get->debug) {
			if ($input->get->text('debug') == 'record') {
				$postrequest = get_request_record($ordn, false);
				unset($postrequest['cc']); unset($postrequest['expiredate']); unset($postrequest['cvc']); 
				unset($postrequest['track1']); unset($postrequest['track2']);
			} else {
				$postrequest = json_decode(file_get_contents($config->paths->content.'test/request.json'));
			}
		} else {
			$postrequest = $input->post->request;
		}
		delete_authorize_responserecord($ordn);
		if (!$input->get->text('debug')) {
			delete_authorize_record($ordn);
			writeauthnetrecord($postrequest, false);
		}
		include ($config->paths->content.$gateway."/$gateway-router.php");
	} else {
		echo json_encode(array('response' => $response));
		exit;
	}


	