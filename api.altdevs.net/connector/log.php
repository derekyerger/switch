<?php require_once("config.php");
require_once("db.php");

$ip = $_SERVER['REMOTE_ADDR'];

if (!isset($_FILES['data'])) return;

$log = file_get_contents($_FILES['data']['tmp_name']);

$id = preg_grep("/^(([0-9]){6} ){2}v[0-9]\.[0-9]-([a-zA-Z0-9]){16}/", explode("\n", $log));

if (count($id) == 0) return;

$id = substr(array_pop($id), 14);

DB::getInstance()->put('INSERT INTO devices (IP, log) VALUES (?, ?)', 'ss', $ip, $log);

?>
