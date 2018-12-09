#!/usr/bin/env php
<?php

if (file_exists('ws/websockets.php')) chdir('ws');
require_once('./websockets.php');

class ttyServer extends WebSocketServer {
  
	protected $buf;

	public function notify($message) {
	}

	protected function process ($user, $message) { }

	protected function connected ($user) { }

	protected function closed ($user) { }

	protected function tick() {
		if (wait_stdin())
			while (($char = fread(STDIN, 1)) != "")
				if ($char == "\n") {
					foreach ($this->users as $user) {
						$this->send($user, $this->buf);
					}
					$this->buf = "";
				} else $this->buf .= $char;
	}
}

function wait_stdin() { /* Returns true if there is data to read. Otherwise
							blocks for up to a second */
	$r = [STDIN];
	$w = $e = null;
	return stream_select($r, $w, $e, 0, 100000) > 0;
}

system("stty -icanon -echo");
stream_set_blocking(STDIN, false);

$tty = new ttyServer("0.0.0.0", $argv[1] ? $argv[1] : "9000");

$tty->run();

?>
