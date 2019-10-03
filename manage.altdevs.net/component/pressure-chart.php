<?php $title = "Pressure chart";
ob_start(); ?>
			<div id="pressure-chart" class="height-sm"></div>
<?php $content = ob_get_clean();
require('elements/panel.php'); $v = explode(",", $comm->txrxCmd(19, "1\n", 1000));
Js::append("
softP = $v[0];
hardP = $v[1];
doPlot = function() {
	'use strict';
	var plot = $.plot($('#pressure-chart'), pressureChart, {
		series: {
			shadowSize: 0,
		},
		yaxis: {
			min: 0,
			tickColor: COLOR_GREY_LIGHTER,
			tickLength: 5
		},
		xaxis: {
			show: false,
			tickColor: COLOR_GREY_LIGHTER
		},
		grid: {
			borderWidth: 1,
			borderColor: COLOR_GREY_LIGHTER,
			backgroundColor: COLOR_SILVER_LIGHTER,
			markings: [
				{ color: '#666', lineWidth:2, yaxis: { from: softP, to: softP } },
				{ color: '#666', lineWidth:2, yaxis: { from: hardP, to: hardP } }
			]
		},
		legend: {
			noColumns: 1,
			show: false
		},
		margin: {
			top: 15,
			bottom: 15
		}
	});
}
");
?>
