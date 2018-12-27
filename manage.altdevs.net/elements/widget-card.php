<div class="col-md-6 col-sm-6">
	<div class="widget-card widget-card-rounded m-b-20" data-id="widget">
	<?php if (isset($card['image'])) { ?>
		<div class="widget-card-cover" style="background-image: url(<?= $card['image'] ?>)"></div>
	<?php } else { ?>
		<div class="widget-card-cover" style="background-color:#333"></div>
	<?php } ?>
		<div class="widget-card-content top">
			<h4 class="text-white m-t-10"><b><?= $card['title'] ?></b></h4>
			<h5 class="f-s-12 text-white-transparent-7 m-b-2"><b><?= $card['subtitle'] ?></b></h5>
		</div>
		<div class="widget-card-content">
			<?= $card['content'] ?>
		</div>
	</div>
</div>
