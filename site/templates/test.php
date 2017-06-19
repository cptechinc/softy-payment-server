<?php
	$ordn = $input->get->text('ordn');
	echo  json_encode(get_request_record($ordn, false)); ?>