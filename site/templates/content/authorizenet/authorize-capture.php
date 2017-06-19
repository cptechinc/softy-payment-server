<?php
	use net\authorize\api\contract\v1 as AnetAPI;
	use net\authorize\api\controller as AnetController;

	 // Set the transaction's refId
    $refId = 'ref' . time();
    $payment = new AnetAPI\PaymentType();
	if ($request['track2'] == "") { //DECODED TRACK NBR
		$retail = new AnetAPI\TransRetailInfoType();
		$retail->setMarketType(\net\authorize\api\constants\ANetEnvironment::MARKET_TYPE_CNP);
		// Create the payment data for a credit card
		$creditcard = new AnetAPI\CreditCardType();
		$creditcard->setCardNumber($request['cc']); //DECODED TRACK NBR
		$creditcard->setExpirationDate(authorizenetdate($request['expiredate']));  //DECODED EXPIRATION DATE
		$creditcard->setCardCode($request['cvc']); //DECODED CVV
		$payment->setCreditCard($creditcard);
		
	} else {
		$retail = new AnetAPI\TransRetailInfoType();
		$retail->setMarketType(\net\authorize\api\constants\ANetEnvironment::MARKET_TYPE_CP); //RETAIL CP
		$retail->setDeviceType(\net\authorize\api\constants\ANetEnvironment::DEVICE_TYPE_PC); //PC REGISTER 
		
		$trackdata = new AnetAPI\CreditCardTrackType();
		$trackdata->setTrack1($request['track1']);
		$trackdata->setTrack2($request['track2']);
		$payment->setTrackData($trackdata);
		
	}
	
	$payment = new AnetAPI\PaymentType();
	$payment->setCreditCard($creditcard);

	$address = new AnetAPI\CustomerAddressType();
	$address->setAddress($request['street']);
	$address->setZip($request['zipcode']);
	

	// Set the customer's identifying information
    $customer = new AnetAPI\CustomerDataType();
    $customer->setType("individual");
    $customer->setId($request['custid']);

    $order = new AnetAPI\OrderType();
    $order->setInvoiceNumber($request['ordernbr']);

	// Create a transaction
	$transactionRequestType = new AnetAPI\TransactionRequestType();
	$transactionRequestType->setTransactionType(\net\authorize\api\constants\ANetEnvironment::TRANSACTION_TYPE_AUTHCAPTURE); 
	$transactionRequestType->setAmount($request['amount']);
	$transactionRequestType->setPayment($payment);
	$transactionRequestType->setBillTo($address);
	$transactionRequestType->setCustomer($customer);
	$transactionRequestType->setOrder($order);
	$transactionRequestType->setRetail($retail);

	$transaction = new AnetAPI\CreateTransactionRequest();
	$transaction->setMerchantAuthentication($merchantAuthentication);
	$transaction->setRefId($refId);
	$transaction->setTransactionRequest($transactionRequestType);
	$controller = new AnetController\CreateTransactionController($transaction);

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