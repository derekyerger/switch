<?php /* Copyright (c) 2018 by Derek Yerger. All Rights Reserved. */

require_once("autoload.php");

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET') { ?>

<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>The Accessible Input Device Configurator</title>
<meta name="copyright" content="Copyright (c) 2018 by Derek Yerger. All Rights Reserved." />
<style type="text/css">
.progress-overlay {
	position: fixed;
	top: 0;
	right: 0;
	bottom: 0;
	left: 0;
	z-index: 101;
	background: #000;
	opacity: .6;
	filter: Alpha(Opacity=60);
}
.progress-overlay span {
	position: fixed;
	margin: auto;
	top: 0;
	left: 0;
	bottom: 0;
	right: 0;
	font-size: 8em;
}
.box {
	position:absolute;
	background-color:transparent;
}
.hcipos {
	width:33%;
	float:left;
	text-align:center;
}
.hcio {
	position:absolute;
	top:0;
	width:33%;
	height:100%;
	background-color:transparent;
}

.dot {
	position: absolute;
	top: 50%;
	width: 0.2em;
	height: 0.2em;
	background-color: #97f4ff;
	box-shadow: 0 0 .2em .13em #52a8e8, 0 0 0 0 transparent, 0 0 0 0 #298df4;
	opacity: 0;
	animation: dot-anim linear 800ms 0ms;
	border-radius: 50%;
}

.dot1 { left: 90% }
.dot2 { left: 10% }
.dot3 { left: 50% }

@keyframes dot-anim {
	0% {
		opacity: 1;
	}
	6% {
		box-shadow: 0 0 .2em .13em #52a8e8, 0 0 0 0 transparent, 0 0 5px 10px #298df4;
	}
	51% {
		opacity: 0;
		box-shadow: 0 0 .2em .13em #52a8e8, 0 0 0 120px transparent, 0 0 0 120px #298df4;
	}
}

.tico {
	width: 1.5rem;
	vertical-align: bottom;
}

.tooltip-help .tooltip-inner {
	background-color: white;
	color: black;
}

.tooltip-help.bs-tooltip-auto[x-placement^=bottom] .arrow::before,
.tooltip-help.bs-tooltip-bottom .arrow::before {
	border-bottom-color: white !important;
}
.tooltip-help.bs-tooltip-auto[x-placement^=top] .arrow::before,
.tooltip-help.bs-tooltip-top .arrow::before {
	border-top-color: white !important;
}
.tooltip-help.bs-tooltip-auto[x-placement^=left] .arrow::before,
.tooltip-help.bs-tooltip-left .arrow::before {
	border-left-color: white !important;
}
.tooltip-help.bs-tooltip-auto[x-placement^=right] .arrow::before,
.tooltip-help.bs-tooltip-right .arrow::before {
	border-right-color: white !important;
}
</style>
<link rel="stylesheet" type="text/css" href="css/fontawesome-all.min.css" media="screen">
<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" media="screen">
<script src="jquery-3.2.1.min.js"></script>
<script src="popper.min.js"></script>
<script src="bootstrap.min.js"></script>
<script src="maps.js"></script>
<script src="index.js"></script>
<script>
var locS = <?php print json_encode(DEV_SENSOR); ?>;
var pS = <?php print json_encode(DEV_IMPULSE); ?>;
var locMap = <?php print json_encode(array_flip(DEV_SENSOR)); ?>;
var pMap = <?php print json_encode(array_flip(DEV_IMPULSE)); ?>;
</script>
</head><body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="javascript:void(0)"><img src="logo.png" style="height:2rem"></a>

  <div class="collapse navbar-collapse" id="navbarColor02">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a id="mStatus" class="nav-link" href="javascript:void(0)" onclick="fetchPage('Status');">Status <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <a id="mConfiguration" class="nav-link" href="javascript:void(0)" onclick="fetchPage('Configuration');">Configuration</a>
      </li>
      <li class="nav-item">
        <a id="mTemplates" class="nav-link" href="javascript:void(0)" onclick="fetchPage('Templates');">Templates</a>
      </li>
      <li class="nav-item">
        <a id="mUserGuide" class="nav-link" href="javascript:void(0)" onclick="fetchPage('User Guide');">User Guide</a>
      </li>
    </ul>
  </div>
  <div class="col main text-right"><i class="fa fa-battery-full" data-original-title="<?php print $comm->txrxCmd(8, "", 1000);?>" data-toggle="tooltip"></i> </div>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor02" aria-controls="navbarColor02" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
</nav>

<div class="container" id="viewport">
	<?php require_once("status.php"); ?>
</div>
<div class="progress-overlay" style="display:none">
	<span class="fa-stack">
		<i class="fa fa-sync fa-spin fa-stack-1x fa-inverse"></i>
	</span>
</div>
<input id="prog" type="hidden" value="<?php
	print http_build_query(
		fetchProgramming($comm), null, "&", PHP_QUERY_RFC3986); ?>">
</body></html><?php
} else { // <~~ GET (view) / POST (ajax) ~~>
	switch ($_POST['f']) {
		case "save":
			$comm->txrxCmd(3, $_POST['d'] . "\n");
			break;

		case "commit":
			$comm->txrxCmd(4);
			print http_build_query(
				fetchProgramming($comm), null, "&", PHP_QUERY_RFC3986);
			break;

		case "get":
		case "getHelp":
			if (($r = $comm->txrxCmd(5, "", 10000)) === false) {
				$comm->txrxCmd(6); /* Cancel capture */
			} else {
				if (substr($r, 0, 1) == "c") $r = $comm->txrxCmd(null, "", 100);
				print $r;
			}
			break;

		case "set":
			$comm->txrxCmd(7, $_POST['d'] . "\n", 10000);
			break;

		case "page":
			$f = strtolower(preg_replace("/ /", "", $_POST['d']));
			if (!file_exists("$f.php")) {
				print "<b>Error</b>";
				break;
			} else require_once("$f.php");
			break;

		case "poll":
			if (($r = $comm->txrxCmd(null, "", 100)) !== false)
				print $r;
			break;
		
		case "delbond":
			$comm->txrxCmd(15);
			break;

		case "undebug":
			$comm->txrxCmd(11, "0\n");
			break;

		case "reset":
			$comm->txrxCmd(12);
			shell_exec("sync; sleep 20");
			break;

		case "getProfile":
			$p = json_decode(file_get_contents("profiles"), true)[$_POST['d']];
			print $p;
			$comm->txrxCmd(3, "$p\n");
			break;

		case "saveProfile":
			$p = json_decode(file_get_contents("profiles"), true);
			if ($_POST['d']['data']) $p[$_POST['d']['name']] = $_POST['d']['data'];
			else unset($p[$_POST['d']['name']]);
			file_put_contents("profiles", json_encode($p));
			break;
	}
} ?>
