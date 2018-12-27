<?php $title = "Profiles and storage";
ob_start(); ?>
		<div class="btn-group">
			<div class="dropdown">
				<button class="btn btn-success dropdown-toggle" type="button" id="ddProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Profile</button>
				<div id="profileSel" class="dropdown-menu" aria-labelledby="ddProfile">
					<?php foreach (array_keys((Array)json_decode(file_get_contents("profiles")), true) as $l)
						print '<a class="dropdown-item" href="javascript:void(0)" onclick="retr(\'getProfile\', \'' . $l . '\');">' . $l . '</a>'; ?>
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
<?php $content = ob_get_clean();
require('elements/panel.php'); ?>
