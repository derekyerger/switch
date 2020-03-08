<?php require('pressurebased.php');
define('DEV_SENSOR', [
	"Left" => 1,
	"Center" => 3,
	"Right" => 2 ]);

define('DEV_IMPULSE', [
	"Soft Tap" => 0,
	"Hard Tap" => 1,
	"Long Press" => 2 ]);

define('DEV_IMAGE', 'devices/aid3.png');

define('DEV_CSS', '
	.dot1 { left: 10% }
	.dot2 { left: 90% }
	.dot3 { left: 50% }
');

define('DEV_HAS_BLUETOOTH', true);

define('DEV_CODENAME', 'Vectis');
?>
