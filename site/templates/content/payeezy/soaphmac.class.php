<?php


	class SoapClientHMAC extends SoapClient {
		var $keyid;
		var $hmackey;
		public function __doRequest($request, $location, $action, $version, $one_way = NULL) {
			global $context; 
			$hashtime = date("c");
			$hashstr = "POST\ntext/xml; charset=utf-8\n" . sha1($request) . "\n" . $hashtime . "\n" . parse_url($location,PHP_URL_PATH);
			$authstr = base64_encode(hash_hmac("sha1",$hashstr,$this->hmackey,TRUE));
			if (version_compare(PHP_VERSION, '5.3.11') == -1) {
				ini_set("user_agent", "PHP-SOAP/" . PHP_VERSION . "\r\nAuthorization: GGE4_API " . $this->keyid . ":" . $authstr . "\r\nx-gge4-date: " . $hashtime . "\r\nx-gge4-content-sha1: " . sha1($request));
			} else {
				stream_context_set_option($context,array("http" => array("header" => "authorization: GGE4_API " . $this->keyid . ":" . $authstr . "\r\nx-gge4-date: " . $hashtime . "\r\nx-gge4-content-sha1: " . sha1($request))));
			}
			return parent::__doRequest($request, $location, $action, $version, $one_way);
		}

		public function SoapClientHMAC($wsdl, $options = NULL, $keyid, $hmackey) {
			global $context;
			$context = stream_context_create();
			$options['stream_context'] = $context;
			$this->hmackey = $hmackey;
			$this->keyid = $keyid;
			return parent::SoapClient($wsdl, $options);
		}
	}
