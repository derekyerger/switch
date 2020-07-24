<?php $title = "Platform";
ob_start();

function platformDdl() {
	$o = '';
	foreach (explode("\n", shell_exec('awk -F \" \'
		/^};$/ { s=0 }
		s==1 && /^\t"/ && !/^\t"\*"/ { print $2 }
		/^var platformMap/ { s=1 }\' maps.js')) as $l)
		if ($l) $o .= '<a class="dropdown-item" href="javascript:void(0)" onclick="ddSet(\'ddPlatform\', \'' . $l . '\');">' . $l . '</a>';
	return $o;
}

?>
<button class="btn btn-primary dropdown-toggle" type="button" id="ddPlatform" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	Default
</button>
<div class="dropdown-menu" aria-labelledby="ddPlatform">
	<?= platformDdl() ?>
</div>
<p>Selecting a specific operating system or platform customizes the available inputs.</p>
<?php $content = ob_get_clean();
require('elements/panel.php'); ?>
<div class="modal modal-message fade" id="platSelect" tabindex="-1" style="display: none;" role="dialog" aria-modal="true" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<p> </p>
			</div>
			<div class="modal-body">
				<h2>Please choose the OS you will be using with the <?= DEV_CODENAME ?></h2>
				<p>Additional features are available depending on which platform you are using. You may also change this from the Status page.</p>
				<br/>
				<div class="row">
					<div class="col">
						<?= platformDdl() ?>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<a href="javascript:;" class="btn btn-primary" data-dismiss="modal" onclick="$('#activitiesDlg').modal('show');">Next</a>
			</div>
		</div>
	</div>
</div>
<?php
if ($_SESSION['platform']) Js::append("ddSet('ddPlatform', '" . $_SESSION['platform'] . "');populateVis();");?>
