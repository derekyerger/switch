<?php $title = "System controls";
ob_start(); ?>
<p>Device ID: <?= $comm->txrxCmd(17, null, 1000) ?></p>
<p>Software version: <?= file_get_contents("/etc/openwrt_version") ?></p>
<button type="button" class="btn btn-primary" onclick="retr('commit');">Save to memory</button>
<!--button type="button" class="btn btn-danger" onclick="retr('debug');">Debugging info</button-->
<button type="button" class="btn btn-primary" onclick="retr('reset');">Reboot Device</button>
<?php $content = ob_get_clean();
require('elements/panel.php'); ?>
<div class="modal fade" id="debugDlg" tabindex="-1" role="dialog" aria-labelledby="actionDlgLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Debug Info</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body responsive-debug">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal" onclick="retr('undebug')">Close</button>
			</div>
		</div>
	</div>
</div>
