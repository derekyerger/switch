<?php $title = "Platform";
ob_start(); ?>
<button class="btn btn-primary dropdown-toggle" type="button" id="ddPlatform" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	Default
</button>
<div class="dropdown-menu" aria-labelledby="ddPlatform">
	<?php
	foreach (explode("\n", shell_exec('awk -F \" \'
		/^};$/ { s=0 }
		s==1 && /^\t"/ && !/^\t"\*"/ { print $2 }
		/^var platformMap/ { s=1 }\' maps.js')) as $l)
		print '<a class="dropdown-item" href="javascript:void(0)" onclick="ddSet(\'ddPlatform\', \'' . $l . '\');">' . $l . '</a>'; ?>
</div>
<p>Selecting a specific operating system or platform customizes the available inputs.</p>
<?php $content = ob_get_clean();
require('elements/panel.php');
if ($_SESSION['platform']) Js::append("ddSet('ddPlatform', '" . $_SESSION['platform'] . "');populateVis();"); ?>
