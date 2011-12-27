<nav class="browse clearfix pagination">
	<?= $this->Paginator->prev('&larr; Newer',array('escape'=>false),null,array('class'=>'hide')); ?>
	<?= $this->Paginator->next('Older &rarr;',array('escape'=>false)); ?>
</nav>

<?php foreach($posts as $post) : $stripe = 0; ?>
<article class="post posttype-quote <?= (($stripe++ % 2) == 0)?'even':'odd'; ?>">
	<div class="row">
		<div class="span3 post-meta">
			<?= $this->Site->profileImage($post); ?>
			
			<span class="post-date">
				<span class="month-and-year"><?= date('F jS, Y',$post['Post']['date']); ?></span>
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
</article><!-- .post -->
<?php endforeach; ?>

<nav class="browse clearfix pagination">
	<?= $this->Paginator->prev('&larr; Newer',array('escape'=>false),null,array('class'=>'hide')); ?>
	<?= $this->Paginator->next('Older &rarr;',array('escape'=>false)); ?>
</nav>