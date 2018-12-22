<?php /* Copyright (c) 2018 by Derek Yerger, for Alternate Devices, LLC. All Rights Reserved. */
require('common.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

	require('header.php');

	require('views/home.php');
	
	?>
	<input id="prog" type="hidden" value="<?php
		print http_build_query(
			fetchProgramming($comm), null, "&", PHP_QUERY_RFC3986); ?>">
	<?php require('footer.php');


} else { // <~~ GET (view) / POST (ajax) ~~>
	switch ($_POST['f']) {
		case "save":
			$comm->txrxCmd(3, $_POST['d'] . "\n");
			break;

		case "commit":
			$comm->txrxCmd(4);
			print http_build_query(
				fetchProgramming($comm), null, "&", PHP_QUERY_RFC3986);
			break;

		case "get":
		case "getHelp":
			if (($r = $comm->txrxCmd(5, "", 10000)) === false) {
				$comm->txrxCmd(6); /* Cancel capture */
			} else {
				if (substr($r, 0, 1) == "c") $r = $comm->txrxCmd(null, "", 100);
				print $r;
			}
			break;

		case "set":
			$comm->txrxCmd(7, $_POST['d'] . "\n", 10000);
			break;

		case "page":
			$f = strtolower(preg_replace("/ /", "", $_POST['d']));
			if (!file_exists("views/$f.php")) {
				break;
			} else echo "$('#content').html('" . preg_replace(["/\n/", "/'/"], ["\\n", "\'"], fetch("views/$f.php")) . "');" .
				"$('.nav li').removeClass('active');$('.nav li span:contains(\"" . $_POST['d'] . "\")').parents('li').addClass('active')";
			break;

		case "poll":
			if (($r = $comm->txrxCmd(null, "", 100)) !== false)
				print $r;
			break;
		
		case "delbond":
			$comm->txrxCmd(15);
			break;

		case "undebug":
			$comm->txrxCmd(11, "0\n");
			break;

		case "reset":
			$comm->txrxCmd(12);
			shell_exec("sync; sleep 20");
			break;

		case "getProfile":
			$p = json_decode(file_get_contents("profiles"), true)[$_POST['d']];
			print $p;
			$comm->txrxCmd(3, "$p\n");
			break;

		case "saveProfile":
			$p = json_decode(file_get_contents("profiles"), true);
			if ($_POST['d']['data']) $p[$_POST['d']['name']] = $_POST['d']['data'];
			else unset($p[$_POST['d']['name']]);
			file_put_contents("profiles", json_encode($p));
			break;
	}
} ?>
