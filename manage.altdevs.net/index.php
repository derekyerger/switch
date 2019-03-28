<?php /* Copyright (c) 2018 by Derek Yerger, for Alternate Devices, LLC. All Rights Reserved. */
require('common.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

	require('header.php');

	require('views/home.php');
	
	/* TODO: per-device cache programming */
	JS::append("deviceData = '" . ($_SESSION['prog'] = http_build_query(fetchProgramming($comm), null, "&", PHP_QUERY_RFC3986)) . "'");
	
	require('footer.php');

} else { // <~~ GET (view) / POST (ajax) ~~>
	switch ($_POST['f']) {
		case "save":
			$comm->txrxCmd(3, $_POST['d'] . "\n");
			break;

		case "commit":
			$comm->txrxCmd(4);
			print "deviceData = '" . http_build_query(
				fetchProgramming($comm), null, "&", PHP_QUERY_RFC3986) . "';";
			print 'swal("Changes saved", "All settings have been saved to permanent storage.", "success")';
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

		case "page":
			/* Strip spaces and anything including/after a period */
			$f = strtolower(preg_replace(["/ /", "/\..*$/"], "", $_POST['d']));
			$subView = (strpos($_POST['d'], ".") ? preg_replace("/^.*\./", "", $_POST['d']) : null);
			if (!file_exists("views/$f.php")) {
				break;
			} else {
				ob_start();
				require("views/$f.php");
				echo "$('#content').html('" . preg_replace(["/\n/", "/'/"], ["\\n", "\'"], ob_get_clean()) . "');" .
				"$('.nav li').removeClass('active');$('.nav li span:contains(\"" . 
					preg_replace("/\..*$/", "", $_POST['d']) . "\")').parents('li').addClass('active');" .
				Js::append() . "$('#page-container')[0].scrollIntoView();";
			}
			break;

		case "component":
			$f = strtolower(preg_replace(["/ /", "/\..*$/"], "", $_POST['d']));
			$subView = (strpos($_POST['d'], ".") ? preg_replace("/^.*\./", "", $_POST['d']) : null);
			if (!file_exists("component/$f.php")) {
				break;
			} else {
				ob_start();
				require("component/$f.php");
				echo "$('#content').append('" . preg_replace(["/\n/", "/'/"], ["\\n", "\'"], ob_get_clean()) . "');" .
					Js::append();
			}
			break;

		case "poll":
			if (($r = $comm->txrxCmd(null, "", 100)) !== false)
				print $r;
			break;
		
		case "delbond":
			$comm->txrxCmd(15);
			break;

		case "debug":
			$comm->txrxCmd(11, "1\n");
			print "$('#debugDlg').modal('show'); if (ws) ws.onmessage = function(msg) { debugStuff(msg); };";
			break;

		case "undebug":
			$comm->txrxCmd(11, "0\n");
			print "ws.onmessage = function(msg) { ping(msg.data); };";
			break;

		case "reset":
			$comm->txrxCmd(12);
			shell_exec("sync; sleep 20");
			break;

		case "getProfile":
			$p = preg_replace("/'/", "\\'", json_decode(file_get_contents("profiles"), true)[$n = $_POST['d']]);
			$comm->txrxCmd(3, "$p\n");
			print "programming = '$p'; ddSet('ddProfile', '$n');";
			break;

		case "saveProfile":
			$p = json_decode(file_get_contents("profiles"), true);
			if ($_POST['d']['data']) $p[$_POST['d']['name']] = $_POST['d']['data'];
			else unset($p[$_POST['d']['name']]);
			file_put_contents("profiles", json_encode($p));
			break;
		
		case "stopMonitor":
			$comm->txrxCmd(19, "0\n", 1000);
			break;
	}
} ?>
