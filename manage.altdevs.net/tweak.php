<?php /* Copyright (c) 2018 by Derek Yerger, for Alternate Devices, LLC. All Rights Reserved. */
require('common.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

	header("Location: /index.php");
	die();

} else { // <~~ GET (view) / POST (ajax) ~~>

	/*if ($_POST['pk'] > 0) {
		http_response_code(500);
		die();
	} TODO: validate */

	$comm->txrxCmd(7, $_POST['pk'] . "," . $_POST['value'] . "\n", DEV_TIMEOUT);

} ?>

