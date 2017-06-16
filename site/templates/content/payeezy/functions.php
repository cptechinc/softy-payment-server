<?php 

	function payeezydate($expdate) {
		if (strpos($expdate, '/') !== FALSE) {
			$date_array = explode("/", $expdate);
			$month = $date_array[0];
			$year = substr($date_array[1], 2);
			return $month."".$year;
		} else {
			return $expdate;
		}
	}
	
	function cardpresent($track1, $track2) {
		if (strlen($track1) > 0 && strlen($track2) > 0) {
			return true;	
		} else {
			return false;	
		}
	}
	
	function wascardpresent($transactionid) {
		if (strpos($transactionid, 'CP') !== false) {
			return true;
		} else if (strpos($transactionid, 'CNP') !== false) {
			return false;
		} else {
			return false;	
		}
	}
	
	function determineterminalaccount($testing, $cardpresent, $request_type, $transactionid) {
		if ($testing) {
			$acct = 'TEST';	
		} else {
			if ($request_type == 'DEBIT') {
				if ($cardpresent) { $acct = 'CP'; } else { $acct = 'CNP';	}
			} elseif ($request_type == 'CREDIT') {
				if (wascardpresent($transactionid)) {
					$acct = 'CP';
				} else {
					$acct = 'CNP';
				}
			}
		}
		return $acct;	
	
	}


?>