<?php $title = "System update file upload";
ob_start(); ?>
<div class="row fileupload-buttonbar">
	<div class="col-md-7">
		<span class="btn btn-primary fileinput-button m-r-3">
			<i class="fa fa-plus"></i>
			<span>Add files...</span>
			<input type="file" name="files[]" multiple>
		</span>
		<button type="submit" class="btn btn-primary start m-r-3">
			<i class="fa fa-upload"></i>
			<span>Start upload</span>
		</button>
		<button type="reset" class="btn btn-default cancel m-r-3">
			<i class="fa fa-ban"></i>
			<span>Cancel upload</span>
		</button>
		<button type="button" class="btn btn-default delete m-r-3">
			<i class="glyphicon glyphicon-trash"></i>
			<span>Delete</span>
		</button>
		<!-- The global file processing state -->
		<span class="fileupload-process"></span>
	</div>
	<!-- The global progress state -->
	<div class="col-md-5 fileupload-progress fade">
		<!-- The global progress bar -->
		<div class="progress progress-striped active m-b-0">
			<div class="progress-bar progress-bar-success" style="width:0%;"></div>
		</div>
		<!-- The extended global progress state -->
		<div class="progress-extended">&nbsp;</div>
	</div>
</div>
<!-- begin table -->
<table class="table table-striped table-condensed">
	<thead>
		<tr>
			<th width="10%">PREVIEW</th>
			<th>FILE INFO</th>
			<th>UPLOAD PROGRESS</th>
			<th width="1%"></th>
		</tr>
	</thead>
	<tbody class="files">
		<tr data-id="empty">
			<td colspan="4" class="text-center text-muted p-t-30 p-b-30">
				<div class="m-b-10"><i class="fa fa-file fa-3x"></i></div>
				<div>No file selected</div>
			</td>
		</tr>
	</tbody>
</table>
<!-- end table -->

<?php $content = ob_get_clean();
require('elements/panel.php'); ?>
