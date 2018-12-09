	<div id="banner" class="page-header">
		<div class="row">
			<div class="col-lg-11 col-md-10 col-sm-9">
				<h1>Device Status</h1>
			</div>
		</div>
	</div>
	<div class="bs-docs-section">
		<div class="row">
			<?php if (!$_SESSION['init']) { 
				$_SESSION['init'] = true; ?>
				<div class="col-auto">
					<div class="alert alert-warning alert-dismissible fade show" role="alert">
						<strong>Welcome!</strong> Please see the <a href="#" onclick="fetchPage('Configuration')" ontouchend="fetchPage('Configuration')">Configuration page</a>
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
				</div>
			<?php } ?>
		</div>
		<!--div class="row">
			<div class="col-lg-11 col-md-10 col-sm-9">
				<p>Battery level: <?php print $comm->txrxCmd(8, "", 1000);
				?></p>
			</div>
		</div-->
		<div class="row">
			<div class="col-lg-11 col-md-10 col-sm-9 responsive-device">
				<img id="sImg" class="img-fluid" src="<?= DEV_IMAGE; ?>">
			</div>
			<div id="sList" class="col-auto responsive-device-txt">
			</div>
		</div>
	</div>

