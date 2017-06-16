<?php
	header('Content-Type: application/json');
	try {
		\Stripe\Stripe::setApiKey(SECRETKEY);
		// Use Stripe's library to make requests...
		 $charge  = \Stripe\Charge::create(array(
			"amount" => 2000,
			"currency" => "usd",
			"description" => "Charge for aubrey.white@example.com",
			"source" => array(
				'object' => 'card',
				'exp_month' => 8,
				'exp_year' => 2018,
				'number' => '4111111111111111',
				'cvc' => '822',
				'name' => 'Pedro Gomez',
				'address_line1' => '214 Valley Green Park',
				'address_zip' => '55352'
			)
		)); 
		// Common setup for API credentials
		
		echo $charge->__toJSON();
	} catch(\Stripe\Error\Card $e) {
		// Since it's a decline, \Stripe\Error\Card will be caught
		$body = $e->getJsonBody();
		$err  = $body['error'];

		print('Status is:' . $e->getHttpStatus() . "\n");
		print('Type is:' . $err['type'] . "\n");
		print('Code is:' . $err['code'] . "\n");
		// param is '' in this case
		print('Param is:' . $err['param'] . "\n");
		print('Message is:' . $err['message'] . "\n");
	} catch (\Stripe\Error\RateLimit $e) {
		// Too many requests made to the API too quickly
		echo 'a';
	} catch (\Stripe\Error\InvalidRequest $e) {
		// Invalid parameters were supplied to Stripe's API
		echo 'b';
	} catch (\Stripe\Error\Authentication $e) {
		// Authentication with Stripe's API failed
		// (maybe you changed API keys recently)
		echo 'c';
	} catch (\Stripe\Error\ApiConnection $e) {
		// Network communication with Stripe failed
		echo 'd';
	} catch (\Stripe\Error\Base $e) {
		// Display a very generic error to the user, and maybe send
		// yourself an email
		echo 'e';
	} catch (Exception $e) {
		// Something else happened, completely unrelated to Stripe
		echo 'f';
	}