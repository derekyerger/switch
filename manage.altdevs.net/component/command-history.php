<?php $title = "Command history";
ob_start(); ?>
			<div class="col-auto responsive-device-txt">
			</div>
<?php $content = ob_get_clean();
require('elements/panel.php');
Js::append("populateLastCmds();"); ?>
