	<div id="banner" class="page-header">
		<div class="row">
			<div class="col-lg-11 col-md-10 col-sm-9">
				<h1>Under the hood</h1>
			</div>
		</div>
	</div>
	<div class="bs-docs-section">
		<div class="row">
			<div class="col-lg-12 col-md-11 col-sm-10">
				<button type="button" class="btn btn-info" onclick="doAjax('undebug');">Return</button>
			</div>
		</div>
		<div class="row">
			<div id="dList" class="col-lg-12 col-md-11 col-sm-10 responsive-debug">
			</div>
		</div>
	</div>
<?php $comm->txrxCmd(11, "1\n"); ?>
