<?php
//LOGIC FOR TAGGED

	if ($taggedrefund) { //IF THIS IS A TAGGED REFUND INCLUDE TAG
		if ($strip == 'CNP') {
			$trxnProperties = array(
				"Ecommerce_Flag" => "7",
				"ExactID" => $payeezy[$acct]['gatewayid'],				    //Payment Gateway
				"Password" => $payeezy[$acct]['gatewaypassword'],						                //Gateway Password
				"Transaction_Type" => $transaction_type,//Transaction Code I.E. Purchase="00" Pre-Authorization="01" etc.
				"Customer_Ref" => $request['ordernbr'], // ORDER NUMBER
				"Language" => "en",
				"Transaction_Tag" => $transactionid,
				"DollarAmount" => $request['amount'],
			);
		} else {
				$trxnProperties = array(
				"Ecommerce_Flag" => "R",
				"ExactID" => $payeezy[$acct]['gatewayid'],				    //Payment Gateway
				"Password" => $payeezy[$acct]['gatewaypassword'],						                //Gateway Password
				"Transaction_Type" => $transaction_type,//Transaction Code I.E. Purchase="00" Pre-Authorization="01" etc.
				"Customer_Ref" => $request['ordernbr'], // ORDER NUMBER
				"Language" => "en",
				"Transaction_Tag" => $transactionid,
				"DollarAmount" => $request['amount'],
			);	
		}
	} else { // IF NOT THEN EXCLUDE TRANSACTION TAG
		if ($strip == 'CNP') {
			$trxnProperties = array(
				"Ecommerce_Flag" => "7",
				"ExactID" => $payeezy[$acct]['gatewayid'],				    //Payment Gateway
				"Password" => $payeezy[$acct]['gatewaypassword'],						                //Gateway Password
				"CardHoldersName" => $request['card_name'],
				"Card_Number" => $request['cc'],
				"Currency" =>  "USD",
				"Expiry_Date" => $request['expiredate'],
				"DollarAmount" => $request['amount'],
				"Transaction_Type" => $transaction_type,//Transaction Code I.E. Purchase="00" Pre-Authorization="01" etc.
				"Customer_Ref" => $request['ordernbr'], // ORDER NUMBER
				"Language" => "en",

			);
		} else {
				$trxnProperties = array(
				"Ecommerce_Flag" => "R",
				"ExactID" => $payeezy[$acct]['gatewayid'],				    //Payment Gateway
				"Password" => $payeezy[$acct]['gatewaypassword'],						                //Gateway Password
				"Transaction_Type" => $transaction_type,//Transaction Code I.E. Purchase="00" Pre-Authorization="01" etc.
				"CardHoldersName" => $request['card_name'],
				"Card_Number" => $request['cc'],
				"Currency" =>  "USD",
				"Expiry_Date" => $request['expiredate'],
				"DollarAmount" => $request['amount'],
				"Customer_Ref" => $request['ordernbr'], // ORDER NUMBER
				"Language" => "en",
			);	
		}


	}

	
	
?>