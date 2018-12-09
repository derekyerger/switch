<div class="modal fade" id="actionDlg" tabindex="-1" role="dialog" aria-labelledby="actionDlgLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="actionDlgLabel">Action Chooser</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="actionSelect">
			</div>
			<div class="modal-body" id="popupKbd" style="display:none">
				<p> Keyboard assignment </p>
				<input id="keys" type="text" style="width: 100%;" />
				<div class="modal-body" style="position:relative;">
					<img src="keyboard.png" class="img-fluid" />
					<div id="keymap"></div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<button id="asSave" type="button" class="btn btn-primary disabled" onclick='$(".btn-assign").click();'>Save changes</button>
			</div>
		</div>
	</div>
</div>
