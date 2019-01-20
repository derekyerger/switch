<?php require('pressurebased.php');
define('DEV_SENSOR', [
	"Left" => 2,
	"Center" => 3,
	"Right" => 1 ]);

define('DEV_IMPULSE', [
	"Soft Tap" => 0,
	"Hard Tap" => 1,
	"Press-and-Hold" => 2 ]);

define('DEV_IMAGE', 'devices/aid2.png');

define('DEV_CSS', '
	.dot1 { left: 90% }
	.dot2 { left: 10% }
	.dot3 { left: 50% }
');

define('DEV_HAS_BLUETOOTH', true);
?>
