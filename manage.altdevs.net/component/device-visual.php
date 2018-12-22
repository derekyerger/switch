<?php $title = "Device visual";
ob_start(); ?>
			<div class="col-lg-11 col-md-10 col-sm-9 responsive-device">
				<img id="sImg" class="img-fluid" src="<?= DEV_IMAGE; ?>">
			</div>
<?php $content = ob_get_clean();
require('elements/panel.php'); ?>
