<?php namespace comms;

/* All devices this system can access */
interface DeviceManager {
	public function getDevIDs(): Array;
	public function getDevByID(String $id): Device;
}

/* A single device */
interface Device {
	public function sendCmd($cmd, $extra = ""); /* Send to device */
	public function readResp($timeout = 500); /* Read from device */
	public function txrxCmd($cmd = null, $extra = "", $timeout = null); /* Send then wait for reply */
}

/* Managers */
abstract class SerialManager implements DeviceManager {

	private $receiverPid;
	private $receiverFile;
	private $devs;

	public abstract function getDevIDs(): Array;

	public function getDevByID(String $id): Device {
		return ($this->devs[$id] ? $this->devs[$id] : ($this->devs[$id] = new SerialDevice($id)));
	}
}

class EmbeddedManager extends SerialManager {
	/* RPi and such */
	public function getDevIDs(): Array {
		return ['/dev/ttyS0'];
	}
}

class LocalManager extends SerialManager {
	public function getDevIDs(): Array {
		return glob('/dev/ttyUSB*');
	}
}

class SocketManager implements DeviceManager {

	private $path;
	private $devs;

	function __construct(String $path) {
		$this->path = $path;
	}

	public function getDevIDs(): Array {
		$r = [];
		foreach (glob($this->path . '/*.tx') as $d) $r[] = substr(basename($d), 0, -3);
		return $r; /* array_walk */
	}
	
	public function getDevByID(String $id): Device {
		return ($this->devs[$id] ? $this->devs[$id] : ($this->devs[$id] = new SocketDevice($this->path . "/$id")));
	}
}

/* Devices */
trait FileBasedReceiver {
	private $rxFile;

	private function wait_file($file, $delay = 100000) { /* Returns true if there is data to read.
								Otherwise blocks for 100 ms */
		#print_r(debug_backtrace());
		fseek($file, $_SESSION['ptr-' . $this->rxFile]);
		usleep($delay);
		$r = fread($file, 1);
		fseek($file, $_SESSION['ptr-' . $this->rxFile]);
		return $r !== ""; // $fs > $_SESSION['fptr'];
	}

	public function readResp($timeout = 500) {
		if (($file = fopen($this->rxFile, "r")) === false) return false;
		for ($i = 0; $i < $timeout / 100; $i++)
			if ($this->wait_file($file)) {
				$buf = "";
				while (($char = fread($file, 1)) != "\n")
					$buf .= $char;
				$_SESSION['ptr-' . $this->rxFile] = ftell($file);
				return $buf;
			}
		
		return false;
	}

	public function txrxCmd($cmd = null, $extra = "", $timeout = null) { /* Send then wait for reply */
		/* This function attempts to lock the tty. The browser uses AJAX to poll and
		 * listen for user inputs. It should be releasing the lock for a time every
		 * 500ms so that any other user actions may be serviced.
		 *
		 * This function is the main intermediate to the tty */
		$r = "";
		if ($cmd !== null) {
			if (!isset($GLOBALS['init'])) {
				/* This is needed once per instantiation. The return value of filesize() will always
				 * be the same value as the first call per instance. Since the receiver process writes
				 * responses to the file after php starts, the functions fseek mustn't be based on
				 * stale information about EOF. If the pointer is reset based on a test such as
				 * (!isset($_SESSION['ptr-' . $this->rxFile]) || $_SESSION['ptr-' . $this->rxFile] > filesize($this->rxFile)) 
				 * then successive calls to this function will always reset the pointer to where it was
				 * when the session began. This is why a global is used. */
				$GLOBALS['init'] = 1;
				$_SESSION['ptr-' . $this->rxFile] = filesize($this->rxFile);
			}

			$this->sendCmd($cmd, $extra);
		}
		if ($timeout !== null) $r = $this->readResp($timeout);
		return $r;
	}

}

class SerialDevice implements Device {

	use FileBasedReceiver;

	private $txFile;
	
	function __construct(String $device) {
		/* Keeps a helper process running to get data from the source and make it
		 * available for websockets. Needs a database backend for scalability, but
		 * a file-based database will do for now */

		$db = file_exists('/tmp/devdb') ? json_decode(file_get_contents('/tmp/devdb'), true) : [];
		
		if (isset($db[$device]) && file_exists('/proc/' . ($db[$device]['pid']))) {
			$this->rxFile = $db[$device]['file'];
		} else {
			if (isset($db[$device]['pid'])) shell_exec("kill " . ($db[$device]['pid']+1) . "; kill " . ($db[$device]['pid']+2));
			$stdbuf = "stdbuf -i0 -o0 -e0";
			$php = trim(shell_exec("which php || which php-cli || echo /usr/bin/php"));
			$db[$device]['file'] = $this->rxFile = $tmp = tempnam('/tmp', 'rx.');
			$db[$device]['port'] = $port = count($db) + 7000;
			$db[$device]['pid'] = trim(shell_exec(
				"stty -F $device icanon 2>&1 >/dev/null;" .
				"$stdbuf cat $device | " . (file_exists('/usr/bin/tee-ts.sh') ? "/usr/bin/tee-ts.sh /tmp/log |" : "") . "tee $tmp | $stdbuf $php ws/ttyws.php $port >/dev/null 2>&1 & echo $!"
			)) - 2;
			file_put_contents('/tmp/devdb', json_encode($db), LOCK_EX); /* flock the bits */
		}
	
		$this->txFile = $device;

	}
	
	public function sendCmd($cmd, $extra = "") { /* Send to device */
		$file = fopen($this->txFile, "w");
		fwrite($file, chr($cmd) . $extra, 1 + strlen($extra));
		fclose($file);
	}

}

class SocketDevice implements Device {

	use FileBasedReceiver;

	private $txFile;
	
	/* An external server will spawn the sockets for us */
	function __construct(String $device) {
		$this->rxFile = "$device.rx";
		$this->txFile = "$device.tx";
	}
	
	public function sendCmd($cmd, $extra = "") { /* Send to device */
		$file = fsockopen("udg://" . $this->txFile, -1, $er, $er2);
		fwrite($file, chr($cmd) . $extra, 1 + strlen($extra));
		fclose($file);
	}

}


?>
