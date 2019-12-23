<?php /* Copyright (c) 2018 by Derek Yerger, for Alternate Devices, LLC. All Rights Reserved. */
require('common.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

	$_SESSION['prog'] = http_build_query(fetchProgramming($comm), null, "&", PHP_QUERY_RFC3986);
	
	/* TODO: per-device cache programming */
	JS::append("deviceData = '" . $_SESSION['prog'] . "';");

	require('header.php');

	require('views/home.php');
	
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
			shell_exec("reboot");
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
		
		case "keepMonitor":
			$comm->txrxCmd(19, "1\n", 1000);
			break;

		case "stopMonitor":
			$comm->txrxCmd(19, "0\n", 1000);
			print "clearTimeout(keepAlive);";
			break;

		case "apset":
			$ssid_inbuilt = substr(shell_exec("uci show wireless.default_radio0.ssid_inbuilt|cut -d= -f2"), 1, -2);
			$key_inbuilt = substr(shell_exec("uci show wireless.default_radio0.key_inbuilt|cut -d= -f2"), 1, -2);

			if ($_POST['d']['ssid'] != $ssid_inbuilt || $_POST['d']['key'] != $key_inbuilt) {
				if (strlen($_POST['d']['ssid']) == 0 || strlen($_POST['d']['ssid']) > 32) $err = "SSID should be 1 to 32 characters";
				if (strlen($_POST['d']['key']) < 8 || strlen($_POST['d']['key']) > 64) $err = "Passphrase should be 8 to 64 characters";
				if ($err) {
					print 'swal("Set credentials", "' . $err . '", "warning");';
				} else {
					shell_exec("uci set wireless.default_radio0.ssid_inbuilt='" . $_POST['d']['ssid'] . "';
					uci set wireless.default_radio0.key_inbuilt='" . $_POST['d']['key'] . "';
					uci commit");

					if (trim(shell_exec("uci show wireless.default_radio0.mode|cut -d= -f2")) == "'ap'") {
						shell_exec("uci set wireless.default_radio0.ssid='" . $_POST['d']['ssid'] . "';
						uci set wireless.default_radio0.key='" . $_POST['d']['key'] . "';
						uci commit; /usr/bin/delaywifi >/dev/null 2>&1 &");
						print 'swal("Set credentials", "Saved successfully. Please reconnect to the device.", "warning");';
					} else {
						print 'swal("Set credentials", "Saved successfully.", "info");';
					}
				}
				print '$("#currentAP").val("' . preg_quote($_POST['d']['ssid'], '"') . '");';
				print '$("#currentPSK").val("' . preg_quote($_POST['d']['key'], '"') . '");';
			}
			break;
		
		case "wifiscan":
			exec('iw dev wlan0 scan |awk -f /usr/bin/wifis.awk', $r);
			$o = '<table class="table table-hover">' . 
				'<thead><tr><td>BSSID</td><td>SSID</td><td>Encryption</td><td>Connect</td></tr></thead><tbody>';
			foreach ($r as $l) {
				$c = explode(" ", $l, 3);
				$o .= "<tr><td>$c[0]</td><td>$c[2]</td><td>$c[1]</td>" .
					'<td><button type="button" class="btn btn-primary" onclick="$(\'#clientDlg\').data({ s: \'' . $c[2] . '\'}).modal(\'show\');">Connect</button></td>';
			}
			$o .= "</tbody></table>";
			print "$('#wifis').html('" . preg_quote($o, "'") . "');";
			break;
		
		case "cliset":
			if (strlen($_POST['d']['ssid']) == 0 || strlen($_POST['d']['ssid']) > 32) $err = "SSID should be 1 to 32 characters";
			if (strlen($_POST['d']['key']) < 8 || strlen($_POST['d']['key']) > 64) $err = "Passphrase should be 8 to 64 characters";
			if ($err) {
				print 'swal("Set credentials", "' . $err . '", "warning");';
			} else {
				$nonce = bin2hex(random_bytes(32));
				shell_exec("uci set wireless.default_radio0.ssid='" . $_POST['d']['ssid'] . "';
				uci set wireless.default_radio0.key='" . $_POST['d']['key'] . "';
				uci set wireless.default_radio0.mode='sta';
				uci set wireless.default_radio0.network='wan';
				uci commit; /usr/bin/delaywifi cli $nonce >/dev/null 2>&1 &");

				print 'swal("Set credentials", "Saved successfully. Please connect to the ' . $_POST['d']['ssid'] . ' network to continue.", "warning");';
				print 'findDevice("' . $nonce . '");';
			}
			break;
		
		case "activateap":
			shell_exec('uci set wireless.default_radio0.ssid=$(uci show wireless.default_radio0.ssid_inbuilt|cut -d= -f2|head -c -2|tail -c +2);
			uci set wireless.default_radio0.key=$(uci show wireless.default_radio0.key_inbuilt|cut -d= -f2|head -c -2|tail -c +2);
			uci set wireless.default_radio0.mode=\'ap\';
			uci set wireless.default_radio0.network=\'lan\';
			uci commit; /usr/bin/delaywifi >/dev/null 2>&1 &');
			
			print 'swal("Set credentials", "Saved successfully. Please reconnect to the access point to continue.", "warning");';
			break;
		
		case "keepAlive":
			$comm->txrxCmd(22);
			break;

		case "ssh":
			shell_exec('/etc/init.d/dropbear start');
			break;
	}
} ?>
