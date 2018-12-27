<?php $title = "System controls";
ob_start(); ?>
<button type="button" class="btn btn-primary" onclick="retr('commit');">Save to memory</button>
<button type="button" class="btn btn-danger" onclick="retr('component', 'Debug');">Debugging info</button>
<button type="button" class="btn btn-danger" onclick="retr('reset');">Reboot Device</button>
<?php $content = ob_get_clean();
require('elements/panel.php'); ?>
