<?php $title = "Pressure monitor";
ob_start(); ?>
			<div class="col-auto">
				<div class="slider-wrapper slider-without-range slider-vertical m-b-0 pull-left m-r-10">
					<input type="text" data-render="powerange-slider" data-hide-range="true" data-vertical="true" data-height="200px" data-min="0" data-max="100" />
				</div>
				<div class="slider-wrapper slider-without-range slider-vertical m-b-0 pull-left m-r-10 blue" style="display: none">
					<input type="text" data-render="powerange-slider" data-hide-range="true" data-vertical="true" data-height="200px" data-min="0" data-max="100" />
				</div>
			</div>
<?php $content = ob_get_clean();
require('elements/panel.php'); $comm->txrxCmd(19, "1\n", 1000);
Js::append("
    if ($('[data-render=\"powerange-slider\"]').length !== 0) {
		var ct = 0;
        $('[data-render=\"powerange-slider\"]').each(function() {
            var option = {}; 
            option.decimal = ($(this).attr('data-decimal')) ? $(this).attr('data-decimal') : false;
            option.disable = ($(this).attr('data-disable')) ? $(this).attr('data-disable') : false;
            option.disableOpacity = ($(this).attr('data-disable-opacity')) ? parseFloat($(this).attr('data-disable-opacity')) : 0.5;
            option.hideRange = ($(this).attr('data-hide-range')) ? $(this).attr('data-hide-range') : false;
            option.klass = ($(this).attr('data-class')) ? $(this).attr('data-class') : ''; 
            option.min = ($(this).attr('data-min')) ? parseInt($(this).attr('data-min')) : 0;
            option.max = ($(this).attr('data-max')) ? parseInt($(this).attr('data-max')) : 100;
            option.start = ($(this).attr('data-start')) ? parseInt($(this).attr('data-start')) : null;
            option.step = ($(this).attr('data-step')) ? parseInt($(this).attr('data-step')) : null;
            option.vertical = ($(this).attr('data-vertical')) ? $(this).attr('data-vertical') : false;
            if ($(this).attr('data-height')) {
                $(this).closest('.slider-wrapper').height($(this).attr('data-height'));
            }   
            pressureSlider.push(new Powerange(this, option));
        }); 
    };
");
?>
