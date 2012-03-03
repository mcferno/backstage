<?php
	$breadcrumbs[] = array(
		'title'=>'Quote Generator',
		'url'=>$this->params->here
	);
	$this->set('breadcrumbs',$breadcrumbs);
?>
<div class="block">
	<h1 class="title" style="margin-top:18px;">Quote Generator</h1>
	<p>Can't wait for fresh content? We'll randomly generate a new quote for you to enjoy.</p>
	<p>We've analyzed our database of quotes and extracted segments to randomly generate new ones. Click the quote below, or refresh the page to see another random generation.</p>
</div>
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