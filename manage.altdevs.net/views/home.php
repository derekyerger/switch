<!-- begin page-header -->
<h1 class="page-header">Home <small>Device Management</small></h1>
<!-- end page-header -->
<?php
if (!isset($subView) || $subView == "status") {
	$prog = preg_replace("/^.*programming=/", "", $_SESSION['prog']);
	if (isset($subView)) $prog = "--";
	require('component/device-visual.php');
	if (!$prog) return; /* Only show device visual on blank device */

	require('component/command-history.php');
}
if (!isset($subView) || $subView == "assignments") {
	require('component/input-assignment.php'); ?>
	<div class="row">
		<div class="col-sm-4">
<?php require('component/platform.php');
if (DEV_HAS_BLUETOOTH) require('component/interface.php'); ?>
		</div>
		<div class="col-sm-8">
<?php require('component/profiles.php'); ?>
		</div>
	</div>
<?php } ?>
