<?php Js::append("$.fn.editable.defaults.mode = 'inline';
$.fn.editable.defaults.inputclass = 'form-control input-sm';
$.fn.editable.defaults.url = '/tweak.php';"); ?>
						<!-- begin table-responsive -->
						<div class="table-responsive">
							<table id="user" class="table table-condensed table-bordered">
								<thead>
									<tr>
										<th width="20%">Field Name</th>
										<th>Field Value</th>
										<th>Description</th>
									</tr>
								</thead>
								<tbody>
								<?php foreach ((Array)$editables as $pk => $props) { ?>
									<tr>
										<td class="bg-black-lighter"><?= $props['title'] ?></td>
										<td><a href="javascript:;" id="ed<?= $pk ?>" data-type="<?= $props['type'] ? $props['type'] : "text" ?>" data-pk="<?= $pk ?>" data-title="<?= $props['data-title'] ?>"><?= $props['value'] ?></a></td>
										<td><span class="text-black-lighter"><?= $props['description'] ?></span></td>
									</tr>
								<?php 
									switch ($props['type']) {
										default:
											Js::append("$('#ed$pk').editable();");
									}
								} ?>
								</tbody>
							</table>
						</div>
						<!-- end table-responsive -->