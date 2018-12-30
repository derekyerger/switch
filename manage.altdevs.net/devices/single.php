<?php require('pressurebased.php');
define('DEV_SENSOR', [
	"Button" => 1 ]);

define('DEV_IMPULSE', [
	"Soft Tap" => 0,
	"Hard Tap" => 1,
	"Press-and-Hold" => 2 ]);

define('DEV_IMAGE', 'devices/single.png');

define('DEV_CSS', '
	.dot1 { left: 50% }
');

define('DEV_HAS_BLUETOOTH', false);
?>
