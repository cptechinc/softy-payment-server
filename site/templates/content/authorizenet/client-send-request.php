<?php
    header('Content-Type: application/json');
	if ($input->get->ordn) {

		$ordn = $input->get->text('ordn');
		delete_authorize_responserecord($ordn);
		$request = get_request_record($ordn, false);
		
		$recordtype = $request['rectype'];
		$transactiontype = $request['type']; //TYPE OF TRANSACTION TO MAKE
		
		if ($recordtype == 'REQ') {
			unset($request['cc']);
			unset($request['expiredate']);
			unset($request['cvc']);
			unset($request['track1']);
			unset($request['track2']);
			$url = 'localhost/paymentservices/test/?ordn='.$ordn;
			$curlfields = array(
				'login' => $user->client_login,
				'password' => $user->client_password,
				'request' => $request
			);
			$response = curlrequest($url, $curlfields);
			$record = json_decode($response);
			writeauthnetrecord($record, false);
			echo json_encode(json_decode($response));
		} // if ($recordtype == 'REQ')
	} // if ($input->get->ordn)