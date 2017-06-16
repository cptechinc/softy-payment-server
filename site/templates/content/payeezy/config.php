<?php 
	$HMACKEY = 'u5W_Wk3uowb3avudDyWWImooWGYfc_iX';
	$KEYID = '351647';
	$GATEWAYID = 'HC0823-96';
	$GATEWAYPASSWORD = '2JAgEyDznBPIaGM5483j09AuABrd1X3q';
	$opts = array( 'http' => array('user_agent' => 'PHPSoapClient' ));
	
	
	$ecomm = array(
		'gatewayid' => 'M83890-82',
		'gatewaypassword' => 'wiDbd8M8tkoI0qAALx1wLu4DVaOKhdF1',
		'hmackey' => 'afeiy5pxk27qsadWDnx9XPwlcwRvngLM',
		'keyid' => '465356',
		'endpoint' => 'https://api.globalgatewaye4.firstdata.com/transaction/v14',
		'wsdl' => 'https://api.globalgatewaye4.firstdata.com/transaction/v14/wsdl'
	);
	
	$retail = array(
		'gatewayid' => 'N49583-02',
		'gatewaypassword' => 'O14y1xevKGZW6kGfumb18j53qFp1N4gQ',
		'hmackey' => 'Pcj5O8USA5h_uN27taxf_spuK3rHm5nz',
		'keyid' => '465355',
		'endpoint' => 'https://api.globalgatewaye4.firstdata.com/transaction/v14',
		'wsdl' => 'https://api.globalgatewaye4.firstdata.com/transaction/v14/wsdl'
	);

	
	$test = array(
		'gatewayid' => 'HC0823-96',
		'gatewaypassword' => '2JAgEyDznBPIaGM5483j09AuABrd1X3q',
		'hmackey' => 'u5W_Wk3uowb3avudDyWWImooWGYfc_iX',
		'keyid' => '351647',
		'endpoint' => 'https://api.demo.globalgatewaye4.firstdata.com/transaction/v14',
		'wsdl' => 'https://api.demo.globalgatewaye4.firstdata.com/transaction/v14/wsdl'
	);
	
	$payeezy = array(
		'TEST' => $test, 
		'CP' => $retail, 
		'CNP' => $ecomm
	);
		

	
	//CNP Card Not Present
	//CP Card Present
	
	
	
?>