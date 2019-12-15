<!-- begin page-header -->
<h1 class="page-header">Templates <small>Predefined action sets</small></h1>
<!-- end page-header -->
					<!-- begin row -->
<?php
$title = "Legend";
$content = '
		<div class="row">
			<div class="col"><img class="tico" src="i0.svg"> Soft tap</div>
			<div class="col"><img class="tico" src="i1.svg"> Hard tap</div>
			<div class="col"><img class="tico" src="i2.svg"> Long press</div>
			<div class="col"><img class="tico" src="i3.svg"> Double tap</div>
		</div>
	';
require('elements/panel.php'); ?>
<?php ob_start(); ?>
					<div class="row row-space-10">
<?php $card = [
	'title' => "Board Games",
	'subtitle' => "Arrow-key based games like Chess, Solitaire, and Minesweeper",
	'image' => "img/board-games.jpg",
	'content' => '
		<div class="row">
			<div class="col"><h4>Left:</h4></div>
			<div class="col text-right"><h4>Right:</h4></div>
		</div>
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
		<br/>
		<button type="button" class="btn btn-info" onclick="template(\'10W;20X;11Z;21Y;12|;22~;\');">Activate</button>
		<!--button type="button" class="btn btn-primary disabled" data-toggle="tooltip" data-placement="bottom" title="Requires Desktop Helper">Assign to app</button-->
	'];
require('elements/widget-card.php');

$card = [
	'title' => "Web Browsing",
	'subtitle' => "A complete set of actions for navigating the internet",
	'image' => "img/web.jpg",
	'content' => '
		<div class="row">
			<div class="col"><h4>Left:</h4></div>
			<div class="col text-center"><h4>Center:</h4></div>
			<div class="col text-right"><h4>Right:</h4></div>
		</div>
		<div class="row">
			<div class="col"><img class="tico" src="i0.svg"> Page down</div>
			<div class="col text-right">Next link <img class="tico" src="i0.svg"></div>
		</div>
		<div class="row">
			<div class="col"><img class="tico" src="i1.svg"> Page up</div>
			<div class="col text-right">Previous link <img class="tico" src="i1.svg"></div>
		</div>
		<div class="row">
			<div class="col"><img class="tico" src="i2.svg"> Back a page</div>
			<div class="col text-right">Follow link <img class="tico" src="i2.svg"></div>
		</div>
		<div class="row">
			<div class="col text-center"><img class="tico" src="i0.svg"> Next tab</div>
		</div>
		<div class="row">
			<div class="col text-center"><img class="tico" src="i1.svg"> Jump 10 links</div>
		</div>
		<div class="row">
			<div class="col text-center"><img class="tico" src="i2.svg"> Random wikipedia<br>page in new tab</div>
		</div>
		<br/>
		<button type="button" class="btn btn-info" onclick="template(\'10!;11+!;12|;20V;21S;22%X;30^!;31!!!!!!!!!!;32^t`https\\\\://en.wikipedia.org/wiki/+special\\\\:+random|;\');">Activate</button>
		<!--button type="button" class="btn btn-primary disabled" data-toggle="tooltip" data-placement="bottom" title="Requires Desktop Helper">Assign to app</button-->
	'];
require('elements/widget-card.php');

$card = [
	'title' => "Office",
	'subtitle' => "Common productivity shortcuts including word processing activities",
	'image' => "img/office.jpg",
	'content' => '
		<div class="row">
			<div class="col"><h4>Left:</h4></div>
			<div class="col text-center"><h4>Center:</h4></div>
			<div class="col text-right"><h4>Right:</h4></div>
		</div>
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
		<br/>
		<button type="button" class="btn btn-info" onclick="template(\'10^c;20^b;11^v;21^i;12^x;22^z;30^Z;31^Y;\');">Activate</button>
		<!--button type="button" class="btn btn-primary disabled" data-toggle="tooltip" data-placement="bottom" title="Requires Desktop Helper">Assign to app</button-->
	'];
require('elements/widget-card.php');

$title = "Standard templates"; ?>
					</div>
<?php $content = ob_get_clean();
require('elements/panel.php'); ?>
