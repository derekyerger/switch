<?php $title = "Device visual";
ob_start();
if (!$prog) {
	$note = [
		'icon' => "far fa-save",
		'title' => "Blank device detected",
		'content' => 'Your device has no programming! Tap anywhere on the device to get started, or <a id="lnk" href="javascript:helpSeq(0);">start the guided tour</a>.'];
	require('elements/note.php');
	Js::append("$('#welcomeDlg').modal('show');");
} ?>
<div class="row">
	<div class="col">
		<div class="widget-card widget-card-rounded m-b-20" data-id="widget">
			<div class="widget-card-cover va" style="background-color:#000"></div>
			<div class="widget-card-content top">
				<img class="tico" src="i0.svg"> Left soft tap
			</div>
			<div class="widget-card-content" id="va-10">
				<button class="btn btn-outline-secondary btn-xs m-r-5 m-b-5" onclick="javascript:proxyAssign('10');">Unassigned</button>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="widget-card widget-card-rounded m-b-20" data-id="widget">
			<div class="widget-card-cover va" style="background-color:#000"></div>
			<div class="widget-card-content top">
				<img class="tico" src="i0.svg"> Center soft tap
			</div>
			<div class="widget-card-content" id="va-30">
				<button class="btn btn-outline-secondary btn-xs m-r-5 m-b-5" onclick="javascript:proxyAssign('30');">Unassigned</button>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="widget-card widget-card-rounded m-b-20" data-id="widget">
			<div class="widget-card-cover va" style="background-color:#000"></div>
			<div class="widget-card-content top">
				<img class="tico" src="i0.svg"> Right soft tap
			</div>
			<div class="widget-card-content" id="va-20">
				<button class="btn btn-outline-secondary btn-xs m-r-5 m-b-5" onclick="javascript:proxyAssign('20');">Unassigned</button>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="widget-card widget-card-rounded m-b-20" data-id="widget">
			<div class="widget-card-cover va" style="background-color:#000"></div>
			<div class="widget-card-content top">
				<img class="tico" src="i1.svg"> Left hard tap
			</div>
			<div class="widget-card-content" id="va-11">
				<button class="btn btn-outline-secondary btn-xs m-r-5 m-b-5" onclick="javascript:proxyAssign('11');">Unassigned</button>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="widget-card widget-card-rounded m-b-20" data-id="widget">
			<div class="widget-card-cover va" style="background-color:#000"></div>
			<div class="widget-card-content top">
				<img class="tico" src="i1.svg"> Center hard tap
			</div>
			<div class="widget-card-content" id="va-31">
				<button class="btn btn-outline-secondary btn-xs m-r-5 m-b-5" onclick="javascript:proxyAssign('31');">Unassigned</button>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="widget-card widget-card-rounded m-b-20" data-id="widget">
			<div class="widget-card-cover va" style="background-color:#000"></div>
			<div class="widget-card-content top">
				<img class="tico" src="i1.svg"> Right hard tap
			</div>
			<div class="widget-card-content" id="va-21">
				<button class="btn btn-outline-secondary btn-xs m-r-5 m-b-5" onclick="javascript:proxyAssign('21');">Unassigned</button>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="widget-card widget-card-rounded m-b-20" data-id="widget">
			<div class="widget-card-cover va" style="background-color:#000"></div>
			<div class="widget-card-content top">
				<img class="tico" src="i2.svg"> Left long press
			</div>
			<div class="widget-card-content" id="va-12">
				<button class="btn btn-outline-secondary btn-xs m-r-5 m-b-5" onclick="javascript:proxyAssign('12');">Unassigned</button>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="widget-card widget-card-rounded m-b-20" data-id="widget">
			<div class="widget-card-cover va" style="background-color:#000"></div>
			<div class="widget-card-content top">
				<img class="tico" src="i2.svg"> Center long press
			</div>
			<div class="widget-card-content" id="va-32">
				<button class="btn btn-outline-secondary btn-xs m-r-5 m-b-5" onclick="javascript:proxyAssign('32');">Unassigned</button>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="widget-card widget-card-rounded m-b-20" data-id="widget">
			<div class="widget-card-cover va" style="background-color:#000"></div>
			<div class="widget-card-content top">
				<img class="tico" src="i2.svg"> Right long press
			</div>
			<div class="widget-card-content" id="va-22">
				<button class="btn btn-outline-secondary btn-xs m-r-5 m-b-5" onclick="javascript:proxyAssign('22');">Unassigned</button>
			</div>
		</div>
	</div>
</div>
<div class="col-lg-11 col-md-10 col-sm-9 responsive-device">
	<img class="img-fluid" src="<?= DEV_IMAGE; ?>">
</div>
<?php $content = ob_get_clean();
require('elements/panel.php');
Js::append("activateElt('visual');"); ?>

<div class="modal modal-message fade" id="welcomeDlg" tabindex="-1" style="display: none;" role="dialog" aria-modal="true" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<p> </p>
			</div>
			<div class="modal-body">
				<center><img src="img/logo2.svg" style="width: 50%"></center>
				<br/>
				<h2>Welcome to <?= DEV_CODENAME ?>!</h2>
				<p>You are using a state-of-the-art accessible input device. This quick-setup wizard can help you get going!</p>
				<p>This wizard will help you with:
					<ul><li>Bluetooth pairing (optional)
					<li>Platform selection (optional)
					<li>Activity selection</ul>
				</p>
			</div>
			<div class="modal-footer">
				<a href="javascript:;" class="btn btn-lg btn-white" data-dismiss="modal">Skip</a>
				<a href="javascript:;" class="btn btn-lg btn-primary" data-dismiss="modal" onclick="$('#btPair').modal('show');">Next</a>
			</div>
		</div>
	</div>
</div>
<div class="modal modal-message fade" id="btPair" tabindex="-1" style="display: none;" role="dialog" aria-modal="true" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<p> </p>
			</div>
			<div class="modal-body">
				<h2>Bluetooth pairing</h2>
				<p><?= DEV_CODENAME ?> can pair with a Bluetooth-enabled device to control it. If you would like to use Bluetooth,
				please pair a device now. This device will be named "<?= DEV_CODENAME ?>".</p>
			</div>
			<div class="modal-footer">
				<a href="javascript:;" class="btn btn-lg btn-primary" data-dismiss="modal" onclick="$('#platSelect').modal('show');">Next</a>
			</div>
		</div>
	</div>
</div>
<div class="modal modal-message fade" id="activitiesDlg" tabindex="-1" style="display: none;" role="dialog" aria-modal="true" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<p> </p>
			</div>
			<div class="modal-body">
				<h2>Activity selection</h2>
				<p><?= DEV_CODENAME ?> will configure itself to be useful for the activities you typically use. Once you choose a few activities,
				<?= DEV_CODENAME ?> will tell you how it is set up. Please tap your desired activities below.</p>
				<div class="row row-space-10">
					<div class="col-lg-3 col-md-4 col-sm-6">
						<a href="javascript:" id="tile0" onclick="tile(0);">
							<div class="widget-card widget-card-rounded m-b-20" data-id="widget">
								<div class="widget-card-cover"></div>
								<div class="widget-card-content top">
									<h4 class="text-white m-t-10"><b>Switch control</b></h4>
								</div>
								<div class="widget-card-content">
									Maps function keys F1-F9 on the paired Bluetooth device
								</div>
							</div>
						</a>
					</div>
					<div class="col-lg-3 col-md-4 col-sm-6">
						<a href="javascript:" id="tile1" onclick="tile(1);">
							<div class="widget-card widget-card-rounded m-b-20" data-id="widget">
								<div class="widget-card-cover"></div>
								<div class="widget-card-content top">
									<h4 class="text-white m-t-10"><b>Web browsing</b></h4>
								</div>
								<div class="widget-card-content">
									Navigate the web
								</div>
							</div>
						</a>
					</div>
					<div class="col-lg-3 col-md-4 col-sm-6">
						<a href="javascript:" id="tile2" onclick="tile(2);">
							<div class="widget-card widget-card-rounded m-b-20" data-id="widget">
								<div class="widget-card-cover"></div>
								<div class="widget-card-content top">
									<h4 class="text-white m-t-10"><b>Window navigation</b></h4>
								</div>
								<div class="widget-card-content">
									Switch between software or apps
								</div>
							</div>
						</a>
					</div>
					<div class="col-lg-3 col-md-4 col-sm-6">
						<a href="javascript:" id="tile3" onclick="tile(3);">
							<div class="widget-card widget-card-rounded m-b-20" data-id="widget">
								<div class="widget-card-cover"></div>
								<div class="widget-card-content top">
									<h4 class="text-white m-t-10"><b>Arrow keys</b></h4>
								</div>
								<div class="widget-card-content">
									Includes four arrow keys and enter/escape
								</div>
							</div>
						</a>
					</div>
					<div class="col-lg-3 col-md-4 col-sm-6">
						<a href="javascript:" id="tile4" onclick="tile(4);">
							<div class="widget-card widget-card-rounded m-b-20" data-id="widget">
								<div class="widget-card-cover"></div>
								<div class="widget-card-content top">
									<h4 class="text-white m-t-10"><b>Maze game</b></h4>
								</div>
								<div class="widget-card-content">
									A built-in game assists in learning how to use soft and hard tap
								</div>
							</div>
						</a>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<a href="javascript:;" class="btn btn-lg btn-primary" data-dismiss="modal" onclick="$('#platSelect').modal('show');">Next</a>
			</div>
		</div>
	</div>
</div>
