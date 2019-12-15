<?php $title = "Input assignment";
ob_start(); ?>
<?php /*
		<div class="btn-group">
			<button id="ddCapture" type="button" class="btn btn-warning m-r-5 m-b-5" onclick="get();">Capture</button>
			<div class="dropdown">
				<button class="btn btn-primary dropdown-toggle m-r-5 m-b-5" type="button" id="ddLocation" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Location</button>
				<div class="dropdown-menu" aria-labelledby="ddLocation">
					<?php
					foreach (array_keys(DEV_SENSOR) as $l)
						print '<a class="dropdown-item" href="javascript:void(0)" onclick="ddSet(\'ddLocation\', \'' . $l . '\');">' . $l . '</a>'; ?>
				</div>
			</div>
			<div class="dropdown">
				<button class="btn btn-primary dropdown-toggle m-r-5 m-b-5" type="button" id="ddImpulse" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Impulse</button>
				<div class="dropdown-menu" aria-labelledby="ddLocation">
					<?php foreach (array_keys(DEV_IMPULSE) as $l)
						print '<a class="dropdown-item" href="javascript:void(0)" onclick="ddSet(\'ddImpulse\', \'' . $l . '\');">' . $l . '</a>'; ?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<p>To assign an action to an input, first choose an input the device is capable of detecting. Click <strong>Capture</strong> to use the next detected input of the device.</p>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<h4>Assign to Action</h4>
			</div>
		</div>
		<div class="btn-group">
			<div class="dropdown">
				<button class="btn btn-primary dropdown-toggle m-r-5 m-b-5" type="button" id="ddLocation" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Default Action
				</button>
				<div class="dropdown-menu" aria-labelledby="ddLocation">
					<a class="dropdown-item" href="javascript:void(0)">Default Action</a>
					<a class="dropdown-item disabled" href="javascript:void(0)">While in "Microsoft Office"</a>
					<a class="dropdown-item disabled" href="javascript:void(0)">While in "Mozilla Firefox"</a>
				</div>
			</div>
			<button id="ddAssignment" type="button" class="btn btn-outline-danger disabled m-r-5 m-b-5" data-target="#actionDlg">Unassigned</button>
			<button id="ddAction" type="button" class="btn btn-info disabled m-r-5 m-b-5" data-target="#actionDlg">Choose Action</button>
		</div>
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<p>Use the action chooser to select what will be done on the attached device when this input is detected. Action groups allow assigning custom sets of inputs based on the current foreground application (requires Desktop Helper application).</p>
			</div>
		</div>
		*/ ?>
<?php $content = ob_get_clean();
// require('elements/panel.php'); ?>
<div class="modal fade" id="actionDlg" tabindex="-1" role="dialog" aria-labelledby="actionDlgLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="actionDlgLabel">Action Chooser</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="actionSelect">
			</div>
			<div class="modal-body" id="popupKbd" style="display:none">
				<p> Keyboard assignment </p>
				<input id="keys" type="text" style="width: 100%;" />
				<div class="modal-body" style="position:relative;">
					<img id="keyboard" src="img/keyboard.png" class="img-fluid" />
					<div id="keymap"></div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<button id="asSave" type="button" class="btn btn-primary disabled" onclick='$(".btn-assign").click();'>Save changes</button>
			</div>
		</div>
	</div>
</div>
<?php Js::append("activateElt('assignment');"); ?>
