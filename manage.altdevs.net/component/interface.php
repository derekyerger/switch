<?php $title = "Interface";
ob_start(); ?>
<div id="bluetooth" class="btn-group btn-group-toggle" data-toggle="buttons">
	<label class="btn btn-secondary">
		<input type="radio" name="bluetooth" id="bluetooth0" autocomplete="off" value="0"> USB HID
	</label>
	<label class="btn btn-secondary">
		<input type="radio" name="bluetooth" id="bluetooth1" autocomplete="off" value="1"> Bluetooth
	</label>
</div>
<br/>
<button type="button" class="btn btn-primary" id="delBond" onclick="delBond();">Clear Bond</button>
<?php $content = ob_get_clean();
require('elements/panel.php');
JS::append("activateElt('interface');"); ?>
