<?php $title = "Network status";

exec("iw dev", $iw_dev);

if (count(preg_grep("/wlan0/", $iw_dev)) == 0) {
	$mode = "Disconnected";
} else {
	$mode = array_pop(explode(" ", trim(array_pop(preg_grep("/^..type/", $iw_dev))))) == "AP" ?
		"Access Point" : "Client";
	$ssid = substr(trim(array_pop(preg_grep("/^..ssid/", $iw_dev))), 5);
}

if (file_exists('/tmp/wifi_error')) $err = file_get_contents('/tmp/wifi_error');

$ssid_inbuilt = substr(shell_exec("uci show wireless.default_radio0.ssid_inbuilt|cut -d= -f2"), 1, -2);
$key_inbuilt = substr(shell_exec("uci show wireless.default_radio0.key_inbuilt|cut -d= -f2"), 1, -2);

ob_start(); ?>
<h3>The <?= DEV_CODENAME ?> can operate in two modes:</h3>
<h4>Access Point</h4>
<p>You can connect any WiFi-enabled device to the <?= DEV_CODENAME ?>. On a laptop or Android-enabled device, you will
not be able to access the internet at the same time unless you have a second WiFi adapter. On iPhones, the internet may
still be accessed via a cellular connection.</p>
<p>This mode of access is useful for occasional configuration, or for accessing from a separate device than the one being
controlled by the <?= DEV_CODENAME ?>.</p>
<h4>Client</h4>
<p>The <?= DEV_CODENAME ?> will connect to your choice of wireless network. The configuration page is then accessible
from your home network, and may be used without affecting internet connectivity.</p>
<p>If the network cannot be located, the <?= DEV_CODENAME ?> will automatically switch back to Access Point mode.</p>
<p>Please use the WiFi network scan applet below to find a network to connect to.</p>
<br/>
<h5>Current mode: <?= $mode ?></h5>
<h5>SSID: <?= $ssid ?></h5>
<?php if ($err) { ?>
<h5>Last error: <?= $err ?></h5>
<br/>
<?php } ?>
<button type="button" class="btn btn-primary" onclick="$('#apDlg').modal('show');">Access point credentials</button>
<?php if ($mode != "Access Point") { ?>
<button type="button" class="btn btn-primary" onclick="retr('activateap');">Disconnect client</button>
<?php }
$content = ob_get_clean();
require('elements/panel.php'); ?>
<div class="modal fade" id="apDlg" tabindex="-1" role="dialog" aria-labelledby="actionDlgLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Access point setup</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="form-group row m-b-15">
					<input type="hidden" id="currentAP" value="<?= $ssid_inbuilt ?>">
					<input type="hidden" id="currentPSK" value="<?= $key_inbuilt ?>">
					<label class="col-form-label col-md-3">SSID</label>
					<div class="col-md-9">
						<input type="text" id="ssid" class="form-control m-b-5" placeholder="SSID">
					</div>
					<label class="col-form-label col-md-3">Passphrase</label>
					<div class="col-md-9">
						<input type="text" id="key" class="form-control m-b-5" placeholder="Passphrase">
						<small class="f-s-12 text-grey-darker">You will have to reconnect after changing these values</small>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal" onclick="retr('apset', { 'ssid': $('#ssid').val(), 'key': $('#key').val() })">Save</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>
<?php Js::append("activateElt('apsettings');"); ?>
