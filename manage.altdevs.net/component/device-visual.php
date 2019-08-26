<?php $title = "Device visual";
ob_start();
if (!$prog) {
	$note = [
		'icon' => "far fa-save",
		'title' => "Blank device detected",
		'content' => 'Your device has no programming! Tap anywhere on the device to get started, or <a id="lnk" href="javascript:helpSeq(0);">start the guided tour</a>.'];
	require('elements/note.php');
} ?>
<div class="col-lg-11 col-md-10 col-sm-9 responsive-device">
	<img class="img-fluid" src="<?= DEV_IMAGE; ?>">
</div>
<?php $content = ob_get_clean();
require('elements/panel.php');
Js::append("activateElt('visual');"); ?>
