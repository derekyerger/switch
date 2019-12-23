<?php
function fetchProgramming(comms\Device $comm) {
	$v = explode("&", substr($comm->txrxCmd(1, "", 1000), 1));
	if (count($v) == 1) return [];
	
	$a = [];
	foreach ($v as $k) {
		$j = explode("=", $k);
		$a[$j[0]] = $j[1];
	}
	$a['programming'] = substr($comm->txrxCmd(2, "", 1000), 1);
	return $a;
}
?>
