<?php $title = "Settings editor";
$editables = [];
foreach (array_filter(explode(";", $comm->txrxCmd(16, "", DEV_TIMEOUT))) as $i) {
	$j = explode(",", $i);
	$editables[] = [
		"title" => $j[0],
		"data-title" => $j[0],
		"value" => $j[1],
		"validate" => "function(value) {
			if (value < $j[2] || value > $j[3]) return '$j[0] must be between $j[2] and $j[3]'; }",
		"description" => $j[4]
	];
}
require('elements/x-editable.php'); ?>
