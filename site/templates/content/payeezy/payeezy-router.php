<?php
	date_default_timezone_set('America/Chicago');
	include $config->paths->content."payeezy/config.php";
	include $config->paths->content."payeezy/functions.php";
	include $config->paths->content."payeezy/soaphmac.class.php";


	$ordn = $input->get->text('ordn');
	$acct = ''; 
	$testing = false; 
	$taggedrefund = false;

	if ($input->get->test) {
		if ($input->get->text('test') == 'y') {
			$testing = true;
		}
	}

	$request = get_request_record($ordn);

	$recordtype = $request['rectype'];
	$transactiontype = $request['type']; //TYPE OF TRANSACTION TO MAKE
	$request['amount'] = str_replace(' ', '', $request['amount']);
	$cardpresent = cardpresent($request['track1'], $request['track2']);

	$acct = determineterminalaccount($testing, $cardpresent, $request_type, $request['trans_id']);
	$strip = determineterminalaccount($testing, $cardpresent, $request_type, $request['trans_id']);

	if ($recordtype == 'REQ') {
		
		switch ($transactiontype) {
			case "DEBIT":
				$transaction_type = '00'; //PURCHASE
				$custid = $request['custid'];
				include $config->paths->content."payeezy-purchase.php";
				break;
			case "CREDIT":
				$cvv = $request['cvc'];
				
				if (strlen($request['trans_id']) > 0 ) { // If we have Transaction ID then we can do a tagged Refund
					$transaction_type = '34'; //TAGGED REFUND
					$taggedrefund = true;
				} else {
					$transaction_type = '04';
					$taggedrefund = false;
				}
				
				$transactionid = str_replace($strip.'-', '', $request['trans_id']);
				include $config->paths->content."payeezy-refund.php";
				break;
			default:
				echo 'Invalid Request type';
				break;	
		}
		
	}

	$client = new SoapClientHMAC($payeezy[$acct]['wsdl'], $opts, $payeezy[$acct]['keyid'], $payeezy[$acct]['hmackey']);

	try {
		$trxnResult = $client->SendAndCommit($trxnProperties);
	} catch (SoapFault $exception) {
		echo "Exception: ".$exception->getMessage()."<br>";
		$sql = write_declined_avs($ordn, 'SOAP', $exception->getMessage(), '');
	}

	if (@$client->fault){
		// there was a fault, inform
		//print "<B>FAULT:  Code: {$client->faultcode} <BR />";
		//print "String: {$client->faultstring} </B>";
		//$trxnResult["CTR"] = "There was an error while processing. No TRANSACTION DATA IN CTR!";
		$sql = write_declined_avs($ordn, $client->faultcode, $client->faultstring, '');
	} else {
		$txresult = json_decode(json_encode($trxnResult), true);
		if ($txresult['Transaction_Approved']) {
			if ($request_type == 'DEBIT' && $cardpresent) {
				$transactionid = 'CP-' . $txresult['Transaction_Tag'];
			} else {
				$transactionid = 'CNP-' . $txresult['Transaction_Tag'];
			}
			echo 'approved';
			$sql = write_approved_response($ordn, $transactionid, $txresult['Authorization_Num'], '');
		} else {
			echo 'not approved<br>';
			echo $txresult['Exact_Resp_Code'] .' - ' . $txresult['Exact_Message'];
			$sql = write_declined_avs($ordn, $txresult['Exact_Resp_Code'], $txresult['Exact_Message'], '');
		}
	}	
	unset($client);


?>