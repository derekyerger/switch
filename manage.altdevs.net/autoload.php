<?php /* temporary hack to decide what platform to use */

require_once("comms/comms.php");

if (file_exists('/www/hciconf')) {
	/* RPi or other OpenWRT-backed device */
	require_once("devices/aid1.php"); /* Assumed */
	$comm = new comms\SerialDevice("/dev/ttyS0");
	define('DEV_TIMEOUT', 1000);
} elseif (file_exists('/dev/ttyUSB0') || file_exists('/dev/ttyACM0')) {
	/* Local direct-attached */
	require_once("devices/aid1.php"); /* Assumed */
	$comm = new comms\SerialDevice(
		file_exists('/dev/ttyUSB0') ? '/dev/ttyUSB0' : '/dev/ttyACM0');
	define('DEV_TIMEOUT', 1000);
} elseif (file_exists('/var/www/altdevs')) {
	/* Cloud platform */
	$devices = new comms\SocketManager('nodes');
	if (count($ids = $devices->getDevIDs()) == 0) {
		echo "<html><body><h1>No devices connected :-(</h1></body></html>";
		exit();
	}
	if (!isset($_SESSION['comm']) || array_search($_SESSION['comm'], $ids) == false)
		$_SESSION['comm'] = array_shift($ids);
	
	$comm = $devices->getDevByID($_SESSION['comm']);
	if (substr($_SESSION['comm'], 0, 4) == "v1.0") require_once("devices/single.php");
	define('DEV_TIMEOUT', 5000);
} else {
	echo "<html><body><h1>Unknown platform :-(</h1></body></html>";
	exit();
}
?>
