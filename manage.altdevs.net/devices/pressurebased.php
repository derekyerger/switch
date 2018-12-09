<?php
function fetchProgramming(comms\Device $comm) {
	$v = explode(",", $comm->txrxCmd(1, "", 1000), 14);
	if (count($v) == 1) return [];
	
	return array(
		"sensorCount" => $v[0],
		"hardPress" => $v[1],
		"softPress" => $v[2],
		"longPress" => $v[3],
		"sampleInterval" => $v[4],
		"settleTime" => $v[5],
		"debounceTime" => $v[6],
		"avgWindow" => $v[7],
		"pressureBias" => $v[8],
		"minGroup" => $v[9],
		"enableAdjust" => $v[10],
		"bluetooth" => $v[11],
		"batterySave" => $v[12],
		"programming" => $v[13]
	);
}
?>
