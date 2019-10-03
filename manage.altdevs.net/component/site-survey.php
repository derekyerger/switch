<?php $title = "Connect to a network";
ob_start(); ?>
<button type="button" class="btn btn-primary" onclick="retr('wifiscan');">Scan for networks</button>
<div id="wifis"></div>
<?php $content = ob_get_clean();
require('elements/panel.php'); ?>
<div class="modal fade" id="clientDlg" tabindex="-1" role="dialog" aria-labelledby="actionDlgLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Connect to network</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="form-group row m-b-15">
					<label class="col-form-label col-md-3">SSID</label>
					<div class="col-md-9">
						<input type="text" id="cli_ssid" class="form-control m-b-5" placeholder="SSID">
					</div>
					<label class="col-form-label col-md-3">Passphrase</label>
					<div class="col-md-9">
						<input type="text" id="cli_key" class="form-control m-b-5" placeholder="Passphrase">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal" onclick="retr('cliset', { 'ssid': $('#cli_ssid').val(), 'key': $('#cli_key').val() })">Connect</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>
<?php Js::append("activateElt('clisettings');"); ?>
