<?php
require_once("autoload.php");

class Js {
	private static $accumulator = "";
	
	public static function append($js = "") {
		self::$accumulator .= $js;
		return self::$accumulator;
	}
}

if (!file_exists('profiles')) file_put_contents('profiles', '');
?>
