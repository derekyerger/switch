	<div id="banner" class="page-header">
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<h1>Device Configuration</h1>
			</div>
		</div>
	</div>
	<div class="bs-docs-section">
		<div class="row">
			<div class="col-lg-10 col-md-9 col-sm-8 col-auto">
				<h2>Basic Configuration</h2>
			</div>
			<div class="col-lg-2 col-md-3 col-sm-4 col-auto text-right">
				<button type="button" class="btn btn-primary" onclick="fetchPage('Advanced');">Advanced</button>
			</div>
		</div>
		<div id="templateLoaded" class="row" style="display:none">
			<div class="col-auto">
				<div class="alert alert-info alert-dismissible fade show" role="alert">
					The template has been loaded successfully.
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<h4>Input Selection</h4>
			</div>
		</div>
		<div class="btn-group">
			<button id="ddCapture" type="button" class="btn btn-warning" onclick="get();">Capture</button>
			<div class="dropdown">
				<button class="btn btn-primary dropdown-toggle" type="button" id="ddLocation" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Location</button>
				<div class="dropdown-menu" aria-labelledby="ddLocation">
					<?php
					foreach (array_keys(DEV_SENSOR) as $l)
						print '<a class="dropdown-item" href="javascript:void(0)" onclick="ddSet(\'ddLocation\', \'' . $l . '\');">' . $l . '</a>'; ?>
				</div>
			</div>
			<div class="dropdown">
				<button class="btn btn-primary dropdown-toggle" type="button" id="ddImpulse" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Impulse</button>
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
				<button class="btn btn-primary dropdown-toggle" type="button" id="ddLocation" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Default Action
				</button>
				<div class="dropdown-menu" aria-labelledby="ddLocation">
					<a class="dropdown-item" href="javascript:void(0)">Default Action</a>
					<a class="dropdown-item disabled" href="javascript:void(0)">While in "Microsoft Office"</a>
					<a class="dropdown-item disabled" href="javascript:void(0)">While in "Mozilla Firefox"</a>
				</div>
			</div>
			<button id="ddAssignment" type="button" class="btn btn-outline-danger disabled" data-target="#actionDlg">Unassigned</button>
			<button id="ddAction" type="button" class="btn btn-info disabled" data-target="#actionDlg">Choose Action</button>
		</div>
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<p>Use the action chooser to select what will be done on the attached device when this input is detected. Action groups allow assigning custom sets of inputs based on the current foreground application (requires Desktop Helper application).</p>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<h4>Profile Storage</h4>
			</div>
		</div>
		<div class="btn-group">
			<div class="dropdown">
				<button class="btn btn-success dropdown-toggle" type="button" id="ddProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Profile</button>
				<div id="profileSel" class="dropdown-menu" aria-labelledby="ddProfile">
					<?php foreach (array_keys((Array)json_decode(file_get_contents("profiles")), true) as $l)
						print '<a class="dropdown-item" href="javascript:void(0)" onclick="doAjax(\'getProfile\', \'' . $l . '\');">' . $l . '</a>'; ?>
				</div>
			</div>
			<button id="profDel" class="btn btn-primary" type="button" onclick="profileRemove();">Delete</button>
			<input id="profName" placeholder="New Unique ID" required="" type="text">
			<button class="btn btn-primary" type="button" onclick="profileAdd();">Add</button>
		</div>
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<p>Store multiple profiles for later retrieval. Profiles are stored on the web interface, and must be manually saved/restored.</p>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<h4>Save to Device Memory</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<button id="ddSave" class="btn btn-primary" type="button" onclick="save();">Save Permanently</button>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<p>Changes made above take effect immediately, but will not be remembered through a power loss until the <strong>Save</strong> button is clicked.</p>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<h4>Keyboard Interface Selection</h4>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<div id="bluetooth" class="btn-group btn-group-toggle" data-toggle="buttons">
					<label class="btn btn-secondary">
						<input type="radio" name="bluetooth" id="bluetooth0" autocomplete="off" value="0"> USB HID
					</label>
					<label class="btn btn-secondary">
						<input type="radio" name="bluetooth" id="bluetooth1" autocomplete="off" value="1"> Bluetooth
					</label>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<p>After choosing Bluetooth, the device will become visible as a Bluetooth keyboard called <strong>AccessibleInputDevice</strong></p>
			</div>
		</div>
	</div>
<?php require_once("c_actionDlg.php"); ?>
