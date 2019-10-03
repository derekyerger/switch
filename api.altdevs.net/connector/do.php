<?php require_once("config.php");
require_once("db.php");

$ip = $_SERVER['REMOTE_ADDR'];
$nonce = $_POST['nonce'];

print array_pop(DB::getInstance()->get('SELECT internalIP FROM requests WHERE nonce = ?', 's', $nonce))['internalIP'];

?>
