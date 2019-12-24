<?php $title = "Profiles and storage";
$prof = array_keys((Array)json_decode(file_get_contents("profiles")), true);
$disc = empty($prof) ? " disabled" : "";
$dist = empty($prof) ? ' disabled="disabled"' : "";
ob_start(); ?>
		<div class="btn-group">
			<div class="dropdown">
				<button class="btn btn-success dropdown-toggle m-r-5 m-b-5<?= $disc ?>" type="button" id="ddProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"<?= $dist ?>>Profile</button>
				<div id="profileSel" class="dropdown-menu" aria-labelledby="ddProfile">
					<?php foreach ($prof as $l)
						print '<a class="dropdown-item" href="javascript:void(0)" onclick="ddSet(\'ddProfile\', \'' . $l . '\');">' . $l . '</a>'; ?>
				</div>
			</div>
			<button id="profLoad" class="btn btn-primary m-r-5 m-b-5 disabled" type="button" onclick="retr('getProfile', $('#ddProfile').html());" disabled="disabled">Load</button>
			<button id="profDel" class="btn btn-primary m-r-5 m-b-5 disabled" type="button" onclick="profileRemove();" disabled="disabled">Delete</button>
			<input id="profName" placeholder="Profile ID" required="" type="text" class="m-r-5 m-b-5">
			<button id="profSave" class="btn btn-primary m-r-5 m-b-5 disabled" type="button" onclick="profileAdd();" disabled="disabled">Save</button>
		</div>
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<p>Store all settings, calibration, and programs to the web server.</p>
			</div>
		</div>
<?php $content = ob_get_clean();
require('elements/panel.php');
Js::append("activateElt('profiles');"); ?>
