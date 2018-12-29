<?php $title = "Astray";
ob_start(); ?>
	<p>Provided by <a href="https://github.com/wwwtyro/Astray">wwwytro on GitHub</a></p>
	<iframe width="100%" height="100%" src="Astray/index.html"></iframe>
<?php $content = ob_get_clean();
require('elements/panel.php');
JS::append("
    $('iframe').height($(window).height()*.7);
    
    $(window).on('resize', function() {
   		$('iframe').height($(window).height()*.7);
    });
");
$comm->txrxCmd(3, "\n"); /* Clears device */
JS::append("if (ws) ws.onmessage = function(msg) {
	console.log(msg.data);
	t = $('iframe')[0].contentWindow.onMoveKey;
	switch (msg.data) {
		case '>20;':
			t([-10, 0]);
			break;
		case '>10;':
			t([10, 0]);
			break;
		case '>21;':
			t([0, -10]);
			break;
		case '>11;':
			t([0, 10]);
			break;
	};
}; ajaxRetFn = 'ajaxRetFn = \"ws.onmessage = function(msg) { ping(msg.data); };\";';");
?>
