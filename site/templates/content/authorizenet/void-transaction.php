<?php 
	use net\authorize\api\contract\v1 as AnetAPI;
  	use net\authorize\api\controller as AnetController;
  
  	define("AUTHORIZENET_LOG_FILE", "phplog");

    
    // Set the transaction's refId
    $refId = 'ref' . time();
    //create a transaction
    $transactionRequest = new AnetAPI\TransactionRequestType();
    $transactionRequest->setTransactionType(\net\authorize\api\constants\ANetEnvironment::TRANSACTION_TYPE_VOID); 
    $transactionRequest->setRefTransId($request['trans_id']);

    $request = new AnetAPI\CreateTransactionRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
	$request->setRefId($refId);
    $request->setTransactionRequest($transactionRequest);

    $controller = new AnetController\CreateTransactionController($request);

    if (AUTHORIZENET_SANDBOX) {
		$response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
	} else {
		$response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
	}
   
	if ($response != null) {
		if ($response->getMessages()->getResultCode() == \net\authorize\api\constants\ANetEnvironment::RESPONSE_OK) {
			$tresponse = $response->getTransactionResponse();

			if ($tresponse != null && $tresponse->getMessages() != null)    { /// SUCCESS
				$display = array(
					'error' => false,
					'authcode' => $tresponse->getAuthCode(),
					'transactionID' => $tresponse->getTransId(),
					'code' => $tresponse->getMessages()[0]->getCode(),
					'description' => $tresponse->getMessages()[0]->getDescription(),
					'avs' => $tresponse->getAvsResultCode(),
					'cvv' => $tresponse->getCvvResultCode()
				);
				$sql = write_approved_response($ordn, $tresponse->getTransId(), $tresponse->getAuthCode(), $tresponse->getAvsResultCode());
				switch($config->servicetype) {
					case 'clientserver':
						echo json_encode($display);
						break;
					case 'server':
						echo json_encode(get_response_record($ordn, false));
						break;
				}
			} else {
				if ($tresponse->getErrors() != null) { // FAIL
					$display = array(
						'error' => true,
						'errorcode' => $tresponse->getErrors()[0]->getErrorCode(),
						'msg' => $tresponse->getErrors()[0]->getErrorText(),
						'avs' => $tresponse->getAvsResultCode(),
					);
					
					$sql = write_declined_avs($ordn, $tresponse->getErrors()[0]->getErrorCode(), $tresponse->getErrors()[0]->getErrorText(), $tresponse->getAvsResultCode() );
					switch($config->servicetype) {
						case 'clientserver':
							echo json_encode($display);
							break;
						case 'server':
							echo json_encode(get_response_record($ordn, false));
							break;
					}
				}
			}
		} else { // fAILED
			$tresponse = $response->getTransactionResponse();

			if ($tresponse != null && $tresponse->getErrors() != null) {
				$display = array(
					'error' => true,
					'errorcode' => $tresponse->getErrors()[0]->getErrorCode(),
					'msg' => $tresponse->getErrors()[0]->getErrorText(),
				);
				write_declined_response($ordn, $tresponse->getErrors()[0]->getErrorCode(), $tresponse->getErrors()[0]->getErrorText());
				switch($config->servicetype) {
					case 'clientserver':
						echo json_encode($display);
						break;
					case 'server':
						echo json_encode(get_response_record($ordn, false));
						break;
				}
			} else {
				$msg = 'No Response';
				$display = array(
					'error' => true,
					'msg' => $msg,
				);
				write_declined_response($ordn, '', $msg);
				switch($config->servicetype) {
					case 'clientserver':
						echo json_encode($display);
						break;
					case 'server':
						echo json_encode(get_response_record($ordn, false));
						break;
				}
			}
		}      
	} else {
		$msg = 'No Response Returned';
		write_declined_response($ordn, '', $msg);
		$display = array(
			'error' => true,
			'msg' => $msg,
		);
		write_declined_response($ordn, '', $msg);
		switch($config->servicetype) {
			case 'clientserver':
				echo json_encode($display);
				break;
			case 'server':
				echo json_encode(get_response_record($ordn, false));
				break;
		}
	}