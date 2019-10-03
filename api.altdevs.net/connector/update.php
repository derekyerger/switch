<?php require_once("config.php");
require_once("db.php");

$ip = $_SERVER['REMOTE_ADDR'];
$nonce = $_POST['nonce'];
$iip = $_POST['ip'];

DB::getInstance()->put('INSERT INTO requests (IP, nonce, internalIP) VALUES (?, ?, ?)', 'sss', $ip, $nonce, $iip);

?>
