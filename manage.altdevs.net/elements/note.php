<div class="note note-<?= isset($note['color']) ? $note['color'] : "lime" ?> m-b-15">
	<?php if (isset($note['icon'])) { ?>
	<div class="note-icon"><i class="<?= $note['icon'] ?>"></i></div>
	<?php } ?>
	<div class="note-content">
		<h4><b><?= $note['title'] ?></b></h4>
		<p>
			<?= $note['content'] ?>
		</p>
	</div>
</div>
