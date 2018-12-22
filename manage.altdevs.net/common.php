<?php
require_once("autoload.php");

function fetch($include) {
	ob_start();
	require($include);
	return ob_get_clean();
} ?>
