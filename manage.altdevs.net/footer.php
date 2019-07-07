		</div>
		<!-- end #content -->
		
		<!-- begin scroll to top btn -->
		<a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
		<!-- end scroll to top btn -->
	</div>
	<!-- end page container -->
	
	<div class="progress-overlay" style="display:none">
		<span class="fa-stack">
			<i class="fa fa-sync fa-spin fa-stack-1x fa-inverse"></i>
		</span>
	</div>

	<!-- ================== BEGIN BASE JS ================== -->
	<script src="js/jquery-3.3.1.min.js"></script>
	<script src="js/jquery-ui.min.js"></script>
	<script src="js/bootstrap.bundle.min.js"></script>
	<!--[if lt IE 9]>
		<script src="js/html5shiv.js"></script>
		<script src="js/respond.min.js"></script>
		<script src="js/excanvas.min.js"></script>
	<![endif]-->
	<script src="js/jquery.slimscroll.min.js"></script>
	<script src="js/powerange.min.js"></script>
	<script src="js/js.cookie.js"></script>
	<script src="js/transparent.min.js"></script>
	<script src="js/apps.min.js"></script>
	<script src="maps.js"></script>
	<script src="index.js"></script>
	<script>
	var locS = <?php print json_encode(DEV_SENSOR); ?>;
	var pS = <?php print json_encode(DEV_IMPULSE); ?>;
	var locMap = <?php print json_encode(array_flip(DEV_SENSOR)); ?>;
	var pMap = <?php print json_encode(array_flip(DEV_IMPULSE)); ?>;
	<?= JS::append() ?>
	</script>
	<!-- ================== END BASE JS ================== -->
	<script src="js/bootstrap-editable.min.js"></script>
	<script src="js/jquery.gritter.min.js"></script>
	<script src="js/sweetalert.min.js"></script>
	<script src="js/jquery.flot.min.js"></script>
	
	<script>
		$(document).ready(function() {
			App.init();
			var images = new Array()
			function preload() {
				for (i = 0; i < preload.arguments.length; i++) {
					images[i] = new Image()
					images[i].src = preload.arguments[i]
				}
			}
			preload(
				"/i0.svg",
				"/i1.svg",
				"/i2.svg",
				"/i3.svg",
				"/img/board-games.jpg",
				"/img/web.jpg",
				"/img/office.jpg",
				"/img/keyboard.png",
			)	
		});
	</script>
</body>
</html>
