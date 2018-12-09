	<div id="banner" class="page-header">
		<div class="row">
			<div class="col-lg-8 col-md-7 col-sm-6">
				<h1>Configuration Templates</h1>
			</div>
		</div>
	</div>
	<div class="bs-docs-section" id="cards">
		<div class="row">
			<div class="col">
				<div class="card" style="width: 14rem;">
					<div class="card-body">
						<h5 class="card-title">Legend</h5>
						<p class="card-text">
							<div class="row">
								<div class="col"><img class="tico" src="i0.svg"> Soft press</div>
							</div>
							<div class="row">
								<div class="col"><img class="tico" src="i1.svg"> Hard press</div>
							</div>
							<div class="row">
								<div class="col"><img class="tico" src="i2.svg"> Press-and-hold</div>
							</div>
							<div class="row">
								<div class="col"><img class="tico" src="i3.svg"> Double tap</div>
							</div>
						</p>
						<button type="button" class="btn btn-warning" onclick="helpSeq(21);">Guide me</button>
					</div>
				</div>
			</div>
			<div class="col">
				<div id="ccCard" class="card" style="width: 18rem;">
					<div class="card-body">
						<h5 id="ccActivity" class="card-title">Board Games</h5>
						<h6 class="card-subtitle mb-2 text-muted">Arrow-key based games like Chess, Solitaire, and Minesweeper</h6>
						<p class="card-text">
							<div class="row">
								<div class="col"><img id="cci0" class="tico" src="i0.svg"> Move left</div>
								<div class="col text-right">Move right <img class="tico" src="i0.svg"></div>
							</div>
							<div class="row">
								<div class="col"><img class="tico" src="i1.svg"> Move down</div>
								<div class="col text-right">Move up <img id="cci1" class="tico" src="i1.svg"></div>
							</div>
							<div class="row">
								<div class="col"><img id="cci2" class="tico" src="i2.svg"> Cancel</div>
								<div class="col text-right">Select <img class="tico" src="i2.svg"></div>
							</div>
						</p>
						<button id="ccActivate" type="button" class="btn btn-info" onclick="template('10W;20X;11Z;21Y;12|;22~;');">Activate</button>
						<button id="ccAssign" type="button" class="btn btn-primary disabled" data-toggle="tooltip" data-placement="bottom" title="Requires Desktop Helper">Assign to app</button>
					</div>
				</div>
			</div>
			<div class="col">
				<div class="card" style="width: 18rem;">
					<div class="card-body">
						<h5 class="card-title">Office</h5>
						<h6 class="card-subtitle mb-2 text-muted">Common productivity shortcuts including word processing activities</h6>
						<p class="card-text">
							<div class="row">
								<div class="col"><img class="tico" src="i0.svg"> Bold</div>
								<div class="col text-right">Copy <img class="tico" src="i0.svg"></div>
							</div>
							<div class="row">
								<div class="col"><img class="tico" src="i1.svg"> Italic</div>
								<div class="col text-right">Paste <img class="tico" src="i1.svg"></div>
							</div>
							<div class="row">
								<div class="col"><img class="tico" src="i2.svg"> Undo</div>
								<div class="col text-right">Cut <img class="tico" src="i2.svg"></div>
							</div>
							<div class="row">
								<div class="col text-center"><img class="tico" src="i0.svg"> Previous Paragraph</div>
							</div>
							<div class="row">
								<div class="col text-center"><img class="tico" src="i1.svg"> Next Paragraph</div>
							</div>
						</p>
						
						<button type="button" class="btn btn-info" onclick="template('10^c;20^b;11^v;21^i;12^x;22^z;30^Z;31^Y;');">Activate</button>
						<button type="button" class="btn btn-primary disabled" data-toggle="tooltip" data-placement="bottom" title="Requires Desktop Helper">Assign to app</button>
					</div>
				</div>
			</div>
			<div class="col">
				<div class="card" style="width: 19rem;">
					<div class="card-body">
						<h5 class="card-title">Web Browsing</h5>
						<h6 class="card-subtitle mb-2 text-muted">Complete set of actions for navigating the internet</h6>
						<p class="card-text">
							<div class="row">
								<div class="col"><img class="tico" src="i0.svg"> Page down</div>
								<div class="col text-right">Next link <img class="tico" src="i0.svg"></div>
							</div>
							<div class="row">
								<div class="col"><img class="tico" src="i1.svg"> Page up</div>
								<div class="col text-right">Next tab <img class="tico" src="i1.svg"></div>
							</div>
							<div class="row">
								<div class="col"><img class="tico" src="i2.svg"> Back a page</div>
								<div class="col text-right">Follow link <img class="tico" src="i2.svg"></div>
							</div>
							<div class="row">
								<div class="col text-center"><img class="tico" src="i2.svg"> Random wikipedia<br>page in new tab</div>
							</div>
						</p>
						<button type="button" class="btn btn-info" onclick="template('10!;20V;11^!;21S;12|;22%X;30^Z;31^Y;32^t`https+\\;//en.wikipedia.org/wiki/+special+\\;+random|;');">Activate</button>
						<button type="button" class="btn btn-primary disabled" data-toggle="tooltip" data-placement="bottom" title="Requires Desktop Helper">Assign to app</button>
					</div>
				</div>
			</div>
			<div class="col">
				<div class="card" style="width: 22rem;">
					<div class="card-body">
						<h5 class="card-title">Presentations</h5>
						<h6 class="card-subtitle mb-2 text-muted">Useful for navigating slide presentations</h6>
						<p class="card-text">
							<div class="row">
								<div class="col"><img class="tico" src="i0.svg"></div>
								<div class="col text-right"><img class="tico" src="i0.svg"></div>
							</div>
							<div class="row">
								<div class="col"><img class="tico" src="i1.svg"> Previous slide</div>
								<div class="col text-right">Next slide <img class="tico" src="i1.svg"></div>
							</div>
							<div class="row">
								<div class="col text-right">Next window <img class="tico" src="i2.svg"></div>
							</div>
						</p>
						<button type="button" class="btn btn-info" onclick="template('10W;20X;11W;21X;12%!;');">Activate</button>
						<button type="button" class="btn btn-primary disabled" data-toggle="tooltip" data-placement="bottom" title="Requires Desktop Helper">Assign to app</button>
					</div>
				</div>
			</div>
			<div class="col">
				<div class="card" style="width: 23rem;">
					<div class="card-body">
						<h5 class="card-title">VIM</h5>
						<h6 class="card-subtitle mb-2 text-muted">The programmer's companion</h6>
						<p class="card-text">
							<div class="row">
								<div class="col"><img class="tico" src="i0.svg"> Previous window</div>
								<div class="col text-right">Next window <img class="tico" src="i0.svg"></div>
							</div>
							<div class="row">
								<div class="col"><img class="tico" src="i1.svg"> Previous screen</div>
								<div class="col text-right">Next screen <img class="tico" src="i1.svg"></div>
							</div>
							<div class="row">
								<div class="col"><img class="tico" src="i2.svg"> Discard quit</div>
								<div class="col text-right">Save quit <img class="tico" src="i2.svg"></div>
							</div>
							<div class="row">
								<div class="col text-center"><img class="tico" src="i0.svg"> Next function</div>
							</div>
							<div class="row">
								<div class="col text-center"><img class="tico" src="i1.svg"> Matching bracket</div>
							</div>
						</p>
						<button type="button" class="btn btn-info" onclick="template('10^ww;20^w+w;11^an;21^ap;12ZZ;22ZQ;30/function|;31\\%;');">Activate</button>
						<button type="button" class="btn btn-primary disabled" data-toggle="tooltip" data-placement="bottom" title="Requires Desktop Helper">Assign to app</button>
					</div>
				</div>
			</div>
			<div class="col">
			</div>
		</div>
	</div>

