<?php 
	function authorizenetdate($date) {
		if (strpos($date, '/') !== FALSE) {
			$datearray = explode('/', $date);
			$month = $datearray[0];
			$year = $datearray[1];
			return $year.'-'.$month;
		} else {
			return $date;
		}
	}