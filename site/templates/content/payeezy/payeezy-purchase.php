<?php
	if ((!$cardpresent)) { //IF TRACK 2 DATA ISN't PRESENT THEN IT'S A CARD NOT PRESENT TRANSACTION
		$trxnProperties = array(
		"Ecommerce_Flag"=>"7",
		  "ExactID" => $payeezy[$acct]['gatewayid'],				    //Payment Gateway
		  "Password" => $payeezy[$acct]['gatewaypassword'],					                //Gateway Password
		  "Transaction_Type" => $transaction_type,//Transaction Code I.E. Purchase="00" Pre-Authorization="01" etc.
		  "Customer_Ref"=>$request['ordernbr'], // ORDER NUMBER
		  "Reference_No" => $request['ordernbr'],
		  "Language" => "en",
		  "DollarAmount" => $request['amount'],
		  "Card_Number" => $request['cc'], //DECRYPTED CARD NBR
		  "Currency" => "USD",
		  "Expiry_Date" => payeezydate($request['expiredate']), //DECRYPTED EXPIREDATE
		  "CardHoldersName" => $request['card_name'],
		  "CVD_Presence_Ind" => "1",
		  "CVDCode" => $request['cvc'],
		  "ZipCode" => $request['zipcode'],
		  "address" => array(
		  	"address1" => $request['street'],
			"zip" => $request['zipcode']
		  ),
		);
	} else {
		$trxnProperties = array(
		"Ecommerce_Flag"=>"R",
		  "ExactID" => $payeezy[$acct]['gatewayid'],				    //Payment Gateway
		  "Password" => $payeezy[$acct]['gatewaypassword'],					                //Gateway Password
		  "Transaction_Type" => $transaction_type,//Transaction Code I.E. Purchase="00" Pre-Authorization="01" etc.
		  "Customer_Ref" => $request['ordernbr'], // ORDER NUMBER
		  "Reference_No" => $request['ordernbr'],
		  "Language" => "en",
		  "Currency"=> "USD",
		  "Track1" => substr($request['track1'], 1, -1),
 		  "Track2" => substr($request['track2'], 1, -1),
		  "DollarAmount" => $request['amount'],
		);
	}
	
?>