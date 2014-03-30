	<div class="row">
		<div class="span3 post-meta">
			<?= $this->Site->profileImage($post); ?>

			<span class="post-date">
				<span class="month-and-year"><?php
					$title = date('F jS, Y', $post['Post']['date']);
					if(!empty($post['Post']['permalink']) && $post['Post']['model'] == 'Twitter') {
						echo $this->Html->link($title,$post['Post']['permalink']);
					} else {
						echo $title;
					}
				?></span>
			</span>
		</div>
		<div class="span10 post-content">
			<div class="quote short-quote"><span><?= strip_tags($post['Post']['body'],'<br/>'); ?></span></div>
			<?php if(!empty($post['Post']['source'])) :?>
			<div class="source">
				&mdash; <?= $post['Post']['source']; ?>
			</div>
			<?php endif; ?>
		</div>
	</div><!-- .row -->
