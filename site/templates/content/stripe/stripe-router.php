<?php
	header('Content-Type: application/json');
	include $config->paths->content.'stripe/config.php';
	include $config->paths->libs.'stripe/vendor/autoload.php';
	include $config->paths->content.'stripe/functions.php';

	if ($input->get->ordn) {
		
		$ordn = $input->get->text('ordn');
		$request = get_request_record($ordn, false);
		
		$recordtype = $request['rectype'];
		$transactiontype = $request['type']; //TYPE OF TRANSACTION TO MAKE
		
		if ($recordtype == 'REQ') {
			
			
			
			switch ($transactiontype) {
				case 'DEBIT': // CHARGE CARD
					include $config->paths->content.'stripe/stripe-capture.php';
					break;
				case 'CREDIT': // REFUND
					include $config->paths->content.'stripe/authorize-refund.php';
					break;
				case 'PREAUTH': // GET CARD AUTHORIZATION
					include $config->paths->content.'stripe/pre-authorize.php';
					break;
				case 'CPREAUTH':
					include $config->paths->content.'stripe/capture-preauthorized.php';
					break;
				default:
					include $config->paths->content.'stripe/stripe-capture.php';
					break;
			}
		} // if ($recordtype == 'REQ')
	} // if ($input->get->ordn)
	else {
		include $config->paths->content.'stripe/stripe-capture.php';
	}
