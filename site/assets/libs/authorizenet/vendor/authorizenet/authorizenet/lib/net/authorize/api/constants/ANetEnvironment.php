<?php
namespace net\authorize\api\constants;

class ANetEnvironment {
    const CUSTOM = "http://wwww.myendpoint.com";
    const SANDBOX = "https://apitest.authorize.net";
    //const PRODUCTION = "https://api.authorize.net/xml/v1/request.api";
    const PRODUCTION = "https://api2.authorize.net";

    const VERSION = "1.9.1";

	const RESPONSE_OK = "Ok";


	const MARKET_TYPE_CP = '2';
	const MARKET_TYPE_CNP = '0';

	const DEVICE_TYPE_PC = '5';

	const TRANSACTION_TYPE_AUTHCAPTURE = 'authCaptureTransaction';
	const TRANSACTION_TYPE_REFUND = 'refundTransaction';
	const TRANSACTION_TYPE_AUTHONLY = 'authOnlyTransaction';
	const TRANSACTION_TYPE_CAPTUREPRIORAUTH = 'priorAuthCaptureTransaction';
}
