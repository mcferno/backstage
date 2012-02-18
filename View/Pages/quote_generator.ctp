<h1 class="title" style="margin-top:18px;">Quote Generator</h1>
<p>Can't wait for fresh content? We'll randomly generate a new quote for you to enjoy.</p>
<article class="post posttype-quote even generator">
	<div class="row">
		<div class="span13 post-content">
			<div class="quote short-quote">
				<?= $quote; ?>
			</div>
		</div>
	</div>
	<?= $this->Html->image('refresh.png',array('alt'=>'','class'=>'refresh jsShow')); ?>
</article>