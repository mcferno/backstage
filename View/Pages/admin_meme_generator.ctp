<?php 
	$this->set('suppressSubnav', true); 
	$this->Html->script(array('meme-generator'),false);
?>
<h1>Meme Generator</h1>
<form class="meme-generator">
	<div class="row no-canvas" style="display:none;">
		<div class="span12">
			<div class="alert alert-error">
				<h2>Your device does not support HTML5 Canvas!</h2>
				<p>Please consider using a more modern browser, such as <a href="https://www.google.com/chrome" target="_blank">Google Chrome</a>.</p>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="span12">
			<input type="text" name="first-line" id="first-line" class="span5" value="First line of text"></input>
		</div>
	</div>
	
	<div class="row">
		<div class="span12">
			<input type="text" name="last-line" id="last-line" class="span5" value="Last line of text"></input>
		</div>
	</div>
	
	<div class="row actions">
		<div class="span12">
			<button class="btn btn-primary save-image"><i class="icon-white icon-refresh"></i> Refresh Image</button>
			<button class="btn btn-inverse live-mode"><i class="icon-white icon-remove"></i> Auto Refresh</button>
		</div>
	</div>
	
	<div class="row output">
		<div class="span12">
			<canvas id="workspace" height="450" width="600"></canvas>
		</div>
	</div>
	<div class="row">
		<div class="span12">
			<button class="btn choose-background"><i class="icon icon-picture"></i> Change Image</button>
		</div>
	</div>
	<div class="row">
		<div class="span12" style="display:none;">
			<div id="backgrounds" class="carousel">
				<div class="carousel-inner">
					<?php $first = true; foreach ($base_images as $image) : ?>
					<div class="item <?if($first) { echo 'active'; } ?>"><?= $this->Html->image($image,array('alt'=>'')); ?></div>
					<?php $first = false; endforeach; ?>
				</div>
				<a class="carousel-control left" href="#backgrounds" data-slide="prev">&lsaquo;</a>
				<a class="carousel-control right" href="#backgrounds" data-slide="next">&rsaquo;</a>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="span12">
			<h3>Image Size</h3>
			<select name="canvasSize" class="canvasSize">
				<option data-width="600" data-height="450" selected="selected">Regular (600 x 450)</option>
				<option data-width="800" data-height="600">Large (800 x 600)</option>
			</select>
		</div>
	</div>
	<div class="row">
		<div class="span12">
			<h3>Tips</h3>
			<ul class="unstyled tips">
				<li><strong>Save</strong> your meme at any time.</li>
				<li><strong>iPhone users</strong> : press and hold on the image and you will be prompted to "<strong>Save Image</strong>".</li>
				<li>Do not use <strong>Live-Mode</strong> if your device is slower, the interface may lag.</li>
			</ul>
			<button class="btn btn-danger reset"><i class="icon-white icon-repeat"></i> Start Over</button>
		</div>
	</div>
</form>