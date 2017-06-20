<?php
	header('Content-Type: application/json');
	include $config->paths->content.'authorizenet/config.php';
	include $config->paths->libs.'authorizenet/vendor/autoload.php';
	include $config->paths->content.'authorizenet/functions.php';
	use net\authorize\api\contract\v1 as AnetAPI;
	use net\authorize\api\controller as AnetController;

	if ($input->get->ordn) {
		
		$ordn = $input->get->text('ordn');
		$request = get_request_record($ordn, false);
		$transactiontype = $request['type']; //TYPE OF TRANSACTION TO MAKE
		if ($request['rectype'] == 'REQ') {
			
			
			
			// Common setup for API credentials
			$merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
			$merchantAuthentication->setName(AUTHORIZENET_API_LOGIN_ID);
			$merchantAuthentication->setTransactionKey(AUTHORIZENET_TRANSACTION_KEY);
			
			switch ($transactiontype) {
				case 'DEBIT': // CHARGE CARD
					include $config->paths->content.'authorizenet/authorize-capture.php';
					break;
				case 'CREDIT': // REFUND
					include $config->paths->content.'authorizenet/authorize-refund.php';
					break;
				case 'PREAUTH': // GET CARD AUTHORIZATION
					include $config->paths->content.'authorizenet/pre-authorize.php';
					break;
				case 'CPREAUTH':
					include $config->paths->content.'authorizenet/capture-preauthorized.php';
					break;
			}
			
		} // if ($recordtype == 'REQ')
	} // if ($input->get->ordn)
