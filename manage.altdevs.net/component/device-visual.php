<?php $title = "Device visual";
ob_start();
if (!$prog) {
	$note = [
		'icon' => "far fa-save",
		'title' => "Blank device detected",
		'content' => 'Your device has no programming! Tap anywhere on the device to get started, or <a id="lnk" href="javascript:helpSeq(0);">start the guided tour</a>.'];
	require('elements/note.php');
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
