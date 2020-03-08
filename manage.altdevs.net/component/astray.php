<?php $title = "Astray";
ob_start(); ?>
	<iframe width="100%" height="100%" src="Astray/index.html"></iframe>
	<p>Provided by <a href="https://github.com/wwwtyro/Astray">wwwytro on GitHub</a></p>
<?php $content = ob_get_clean();
require('elements/panel.php');
JS::append("
    $('iframe').height($(window).height()*.9);
    
    $(window).on('resize', function() {
   		$('iframe').height($(window).height()*.9);
    });
");
$comm->txrxCmd(3, "\x0\n"); /* Clears device */
JS::append("$.extend($.gritter.options, { fade_in_speed: 0 }); if (ws) ws.onmessage = function(msg) {
	console.log(msg.data);
	t = $('iframe')[0].contentWindow.onMoveKey;
	switch (msg.data) {
		case '>10;':
			t([-15, 0]);
			$('.gritter-item-wrapper').remove();
			$.gritter.add({
				title: 'Soft tap left',
				text: 'Go left',
				image: 'i0.svg',
				sticky: false,
				time: ''
			});
			break;
		case '>20;':
			t([15, 0]);
			$('.gritter-item-wrapper').remove();
			$.gritter.add({
				title: 'Soft tap right',
				text: 'Go right',
				image: 'i0.svg',
				sticky: false,
				time: ''
			});
			break;
		case '>11;':
			t([0, -15]);
			$('.gritter-item-wrapper').remove();
			$.gritter.add({
				title: 'Hard tap left',
				text: 'Go down',
				image: 'i1.svg',
				sticky: false,
				time: ''
			});
			break;
		case '>21;':
			t([0, 15]);
			$('.gritter-item-wrapper').remove();
			$.gritter.add({
				title: 'Hard tap right',
				text: 'Go up',
				image: 'i1.svg',
				sticky: false,
				time: ''
			});
			break;
		case '>12;':
			$('.gritter-item-wrapper').remove();
			$.gritter.add({
				title: 'Long press left',
				text: 'Does nothing! Try tapping.',
				image: 'i2.svg',
				sticky: false,
				time: ''
			});
			break;
		case '>22;':
			$('.gritter-item-wrapper').remove();
			$.gritter.add({
				title: 'Long press right',
				text: 'Does nothing! Try tapping.',
				image: 'i2.svg',
				sticky: false,
				time: ''
			});
			break;
		case '>30;':
		case '>31;':
			$('.gritter-item-wrapper').remove();
			$.gritter.add({
				title: 'Center input',
				text: 'Try left or right inputs.',
				image: 'i2.svg',
				sticky: false,
				time: ''
			});
			break;
		case '>32;':
			retr('page', 'Home');
			break;
	};
}; ajaxRetFn = 'ajaxRetFn = \"ws.onmessage = function(msg) { ping(msg.data); };\";';");
?>
