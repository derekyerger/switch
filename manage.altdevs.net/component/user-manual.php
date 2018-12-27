<?php $title = "User manual";
ob_start(); ?>
				<p>Help is available in a few convenient forms, including a guided tour and an online user manual.</p>
				<h3>Guided Tour</h3>
				<p>Click the below button to initiate the guided tour. During the tour, <img class="tico" src="i0.svg"> soft or <img class="tico" src="i1.svg"> hard press the right side of the device to advance the tour, or left side to go back. Any <img class="tico" src="i2.svg"> press-and-hold input cancels the tour.</p>
				<button type="button" class="btn btn-warning" onclick="helpSeq(0);">Guided Tour</button>
				<h3>User Manual</h3>
				<a id="um" href="aid-um.pdf">Download the user manual</a>
<?php $content = ob_get_clean();
require('elements/panel.php'); ?>
