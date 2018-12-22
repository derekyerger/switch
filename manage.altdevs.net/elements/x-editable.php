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
									<tr>
										<td class="bg-silver-lighter">Username</td>
										<td><a href="javascript:;" id="username" data-type="text" data-pk="1" data-title="Enter Username">superuser </a></td>
										<td><span class="text-black-lighter">Simple text field </span></td>
									</tr>
									<tr>
										<td class="bg-silver-lighter">Firstname</td>
										<td><a href="javascript:;" id="firstname" data-type="text" data-pk="1" data-placement="right" data-placeholder="Required" data-title="Enter your Firstname"></a></td>
										<td><span class="text-black-lighter">Required text field, originally empty </span></td>
									</tr>
									<tr>
										<td class="bg-silver-lighter">Sex</td>
										<td><a href="javascript:;" id="sex" data-type="select" data-pk="1" data-value="" data-title="Select sex"></a></td>
										<td><span class="text-black-lighter">Select, loaded from js array. Custom display</span></td>
									</tr>
									<tr>
										<td class="bg-silver-lighter">Group</td>
										<td><a href="javascript:;" id="group" data-type="select" data-pk="1" data-value="5" data-source="/groups" data-title="Select group">Admin</a></td>
										<td><span class="text-black-lighter">Select, loaded from server. <strong>No buttons</strong> mode </span></td>
									</tr>
									<tr>
										<td class="bg-silver-lighter">Error While Loading</td>
										<td><a href="javascript:;" id="status" data-type="select" data-pk="1" data-value="0" data-source="/status" data-title="Select status">Active </a></td>
										<td><span class="text-black-lighter">Error when loading list items</span></td>
									</tr>
									<tr>
										<td class="bg-silver-lighter">Plan vacation?</td>
										<td><a href="javascript:;" id="vacation" data-type="date" data-viewformat="dd.mm.yyyy" data-pk="1" data-placement="right" data-title="When you want vacation to start?">25.02.2013</a></td>
										<td><span class="text-black-lighter">Datepicker </span></td>
									</tr>
									<tr>
										<td class="bg-silver-lighter">Date of birth</td>
										<td><a href="javascript:;" id="dob" data-type="combodate" data-value="1984-05-15" data-format="YYYY-MM-DD" data-viewformat="DD/MM/YYYY" data-template="D / MMM / YYYY" data-pk="1" data-title="Select Date of birth"></a></td>
										<td><span class="text-black-lighter">Date field (combodate) </span></td>
									</tr>
									<tr>
										<td class="bg-silver-lighter">Setup event</td>
										<td><a href="javascript:;" id="event" data-type="combodate" data-template="D MMM YYYY HH:mm" data-format="YYYY-MM-DD HH:mm" data-viewformat="MMM D, YYYY, HH:mm" data-pk="1" data-title="Setup event date and time"></a></td>
										<td><span class="text-black-lighter">Datetime field (combodate)</span></td>
									</tr>
									<tr>
										<td class="bg-silver-lighter">Meeting start</td>
										<td><a href="javascript:;" id="meeting_start" data-type="datetime" data-pk="1" data-url="/post" data-placement="right" data-title="Set date & time">15/03/2013 12:45</a></td>
										<td><span class="text-black-lighter">Bootstrap datetime</span></td>
									</tr>
									<tr>
										<td class="bg-silver-lighter">Comments</td>
										<td>
											<a href="javascript:;" id="comments" data-type="textarea" data-pk="1" data-placeholder="Your comments here..." data-original-title="Enter comments">awesome<br />user!</a></td>
										<td>
											<span class="text-black-lighter">
											Textarea. Buttons below. Submit by <i>ctrl+enter</i>
											</span>
										</td>
									</tr>
									<tr>
										<td class="bg-silver-lighter">Type State</td>
										<td><a href="javascript:;" id="state" data-type="typeaheadjs" data-pk="1" data-placement="right" data-title="Start typing State.."></a></td>
										<td><span class="text-black-lighter">Twitter typeahead.js</span></td>
									</tr>
									<tr>
										<td class="bg-silver-lighter">Fresh fruits</td>
										<td><a href="javascript:;" id="fruits" data-type="checklist" data-value="1,2" data-title="Select fruits"></a></td>
										<td><span class="text-black-lighter">Checklist</span></td>
									</tr>
									<tr>
										<td class="bg-silver-lighter">Tags</td>
										<td><a href="javascript:;" id="tags" data-type="select2" data-pk="1" data-title="Enter tags">html, javascript </a></td>
										<td><span class="text-black-lighter">Select2 (tags mode) </span></td>
									</tr>
									<tr>
										<td class="bg-silver-lighter">Country</td>
										<td><a href="javascript:;" id="country" data-type="select2" data-pk="1" data-value="BS" data-title="Select country"></a></td>
										<td><span class="text-black-lighter">Select2 (dropdown mode)</span></td>
									</tr>
									<tr>
										<td class="bg-silver-lighter">Address</td>
										<td><a href="javascript:;" id="address" data-type="address" data-pk="1" data-title="Please, fill address"></a></td>
										<td><span class="text-black-lighter">Your custom input, several fields</span></td>
									</tr>
									<tr>
										<td class="bg-silver-lighter">Notes</td>
										<td>
											<div id="note" data-pk="1" data-type="wysihtml5" data-toggle="manual" data-title="Enter notes">
												<h3>WYSIWYG</h3>
												 WYSIWYG means <i>What You See Is What You Get</i>.<br />
												 But may also refer to:
												<ul>
													<li>
														 WYSIWYG (album), a 2000 album by Chumbawamba
													</li>
													<li>
														 "Whatcha See is Whatcha Get", a 1971 song by The Dramatics
													</li>
													<li>
														 WYSIWYG Film Festival, an annual Christian film festival
													</li>
												</ul>
												<p><i>Source:</i><a href="http://en.wikipedia.org/wiki/WYSIWYG_%28disambiguation%29">wikipedia.org</a></p>
											</div>
										</td>
										<td>
											<a href="javascript:;" id="pencil"><i class="fa fa-pencil"></i> [edit]</a>
											<br />
											<span class="text-black-lighter">Wysihtml5 (bootstrap only).<br />Toggle by another element</span>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<!-- end table-responsive -->
