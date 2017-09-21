<?php

	function logsend($type, $date, $data) {
		$sql = wire('database')->prepare("INSERT INTO sendlogs (sendtype, datetimestamp, data) VALUES (:sendtype, :date, :data)");
		$switching = array(':sendtype' => $type, ':date' => $date, ':data' => $data);
		$withquotes = array(true, true, true);
		$sql->execute($switching);
		return returnsqlquery($sql->queryString, $switching, $withquotes);
	}

	function get_request_record($ordn, $debug) {
		$sql = wire('database')->prepare("SELECT authnet.*,  cast(aes_decrypt(cardnbr, hex(ordernbr)) as char charset utf8) as cc, cast(aes_decrypt(expdate, hex(ordernbr)) as char charset utf8) as expiredate, cast(aes_decrypt(cvv, hex(ordernbr)) as char charset utf8) as cvc, cast(aes_decrypt(track_ii, hex(ordernbr)) as char charset utf8) as track2, cast(aes_decrypt(track_i, hex(ordernbr)) as char charset utf8) as track1 FROM authnet WHERE ordernbr = :ordn LIMIT 1");
		$switching = array(':ordn' => $ordn); $withquotes = array(true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetch(PDO::FETCH_ASSOC);
		}
	}

	function get_response_record($ordn, $debug) {
		$sql = wire('database')->prepare("SELECT * FROM authnet WHERE ordernbr = :ordn AND rectype = 'RES' LIMIT 1");
		$switching = array(':ordn' => $ordn); $withquotes = array(true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetch(PDO::FETCH_ASSOC);
		}
	}

	function write_approved_response($ordn, $transactionid, $authcode, $avscode) {
		$date = date('Ymd'); $time = date('His');
		$sql = wire('database')->prepare("INSERT INTO authnet (ordernbr, rectype, trans_id, authcode, avs_msg, result, date, time) VALUES (:ordn, 'RES', :transactionid, :authcode, :avscode, 'APPROVED', :date, :time)");
		$switching = array(':ordn' => $ordn, ':transactionid' => $transactionid, ':authcode' => $authcode, ':avscode' => $avscode, ':date' => $date, ':time' => $time);
		$withquotes = array(true, true, true, true, true, true);
		$sql->execute($switching);
		return returnsqlquery($sql->queryString, $switching, $withquotes);
	}
	
	function write_declined_response($ordn, $errorcode, $errormsg) {
		$date = date('Ymd'); $time = date('His');
		$sql = wire('database')->prepare("INSERT INTO authnet (ordernbr, rectype, error_code, error_msg, result, date, time) VALUES (:ordn, 'RES', :errorcode, :errormsg, 'DECLINED', :date, :time)");
		$switching = array(':ordn' => $ordn, ':errorcode' => $errorcode, ':errormsg' => $errormsg, ':date' => $date, ':time' => $time);
		$withquotes = array(true, true, true, true, true);
		$sql->execute($switching);
		return returnsqlquery($sql->queryString, $switching, $withquotes);
	}
	
	function write_declined_avs($ordn, $errorcode, $errormsg, $avscode) {
		$date = date('Ymd'); $time = date('His');
		$sql = wire('database')->prepare("INSERT INTO authnet (ordernbr, rectype, error_code, error_msg, avs_msg, result, date, time) VALUES (:ordn, 'RES', :errorcode, :errormsg, :avscode, 'DECLINED', :date, :time)");
		$switching = array(':ordn' => $ordn, ':errorcode' => $errorcode, ':errormsg' => $errormsg, ':avscode' => $avscode, ':date' => $date, ':time' => $time);
		$withquotes = array(true, true, true, true, true, true);
		$sql->execute($switching);
		return returnsqlquery($sql->queryString, $switching, $withquotes);
	}
	
	function delete_authorize_record($ordn) {
		$sql = wire('database')->prepare("DELETE FROM authnet WHERE ordernbr = :ordn and rectype = 'REQ'");
		$switching = array(':ordn' => $ordn); $withquotes = array(true);
		$sql->execute($switching);
		return returnsqlquery($sql->queryString, $switching, $withquotes);
	}

	function delete_unused_records() {
		$sql = wire('database')->prepare("CALL remove_old_payments()");
		$sql->execute();
	}

	function returnsqlquery($sql, $oldtonew, $havequotes) {
		$i = 0;
		foreach ($oldtonew as $old => $new) {
			if ($havequotes[$i]) {
				$sql = str_replace($old, "'".$new."'", $sql);
			} else {
				$sql = str_replace($old, $new, $sql);
			}
			$i++;
		}
		return $sql;
	}

	function writeauthnetrecord($request, $debug) {
		$query = preparerequest($request);
 		$sql = wire('database')->prepare("INSERT INTO authnet (".$query['columns'].") VALUES(".$query['preparedvalues'].")");
 		$switching = $query['switching']; $withquotes = $query['withquotes'];
 		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		}
	}

	function preparerequest($request) {
		$withquotes = $switching = array();
		$columnlist = $preparedvalues = '';
		foreach ($request as $column => $value) {
			$columnlist .= $column . ',';
			$preparedvalues .= ':'.$column . ',';
			$switching[':'.$column] = $value;
			$withquotes[] = true;
		}
		$columnlist = rtrim($columnlist, ',');
		$preparedvalues = rtrim($preparedvalues,',');
		$request = array('columns' => $columnlist, 'preparedvalues' => $preparedvalues, 'switching' => $switching, 'withquotes' => $withquotes);
		return $request;
	}

	function delete_authorize_responserecord($ordn) {
		$sql = wire('database')->prepare("DELETE FROM authnet WHERE ordernbr = :ordn and rectype = 'RES'");
		$switching = array(':ordn' => $ordn); $withquotes = array(true);
		$sql->execute($switching);
		return returnsqlquery($sql->queryString, $switching, $withquotes);
	}