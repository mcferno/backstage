<?php
	$this->Html->script(array('meme-generator.js?t='.filemtime(JS.'meme-generator.js')),false);
	
	foreach ($base_images as &$image) {
		$image = $this->Html->webroot('img/'.$image);
	}
?>
<script>
var memeBaseImages = <?php echo json_encode($base_images); ?>;
</script>
<form class="meme-generator">
<h1>Meme Generator</h1>
	<div class="row no-canvas" style="display:none;">
		<div class="span8">
			<div class="alert alert-error">
				<h2>Your device does not support HTML5 Canvas!</h2>
				<p>Please consider using a more modern browser, such as <a href="https://www.google.com/chrome" target="_blank">Google Chrome</a>.</p>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="span8">
			<input type="text" name="first-line" id="first-line" class="span5" value="" placeholder="First line of text"></input>
		</div>
	</div>
	
	<div class="row">
		<div class="span8">
			<input type="text" name="last-line" id="last-line" class="span5" value="" placeholder="Last line of text"></input>
		</div>
	</div>
	
	<div class="row actions">
		<div class="span8">
			<button class="btn btn-primary save-image"><i class="icon-white icon-refresh"></i> Refresh<span class="extra"> Image</span></button>
			<button class="btn btn-inverse live-mode"><i class="icon-white icon-remove"></i> Auto<span class="extra"> Refresh</span></button>
			<button class="btn choose-background"><i class="icon icon-picture"></i><span class="extra"> Change Image</span></button>
		</div>
	</div>
	
	<div class="row output">
		<div class="span8">
			<canvas id="workspace" height="450" width="600"></canvas>
		</div>
	</div>
	<div class="row">
		<div class="span8">
			
		</div>
	</div>
	<?php /*
	<div class="row">
		<div class="span8" style="display:none;">
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
	*/ ?>
	<div class="row">
		<div class="span8">
			<button class="btn btn-success save" data-loading-text="Saving ..."><i class="icon-white icon-download"></i> Save on Server</button>
			<button class="btn btn-danger reset"><i class="icon-white icon-ban-circle"></i> Start Over</button>
		</div>
	</div>
	<div class="row">
		<div class="span8">
			<h3>Image Size</h3>
			<select name="canvasSize" class="canvasSize">
				<optgroup label="Horizontal" class="horizontal" data-orientation="horizontal">
					<option data-width="600" data-height="450" selected="selected">Regular (600 x 450)</option>
					<option data-width="800" data-height="600">Large (800 x 600)</option>
				</optgroup>
				<optgroup label="Vertical" class="vertical" data-orientation="vertical">
					<option data-width="450" data-height="600">Regular (600 x 450)</option>
					<option data-width="600" data-height="800">Large (600 x 800)</option>
				</optgroup>
			</select>
		</div>
	</div>
	<div class="row">
		<div class="span8">
			<h3>Tips</h3>
			<ul class="unstyled tips">
				<li><strong>Save</strong> your meme at any time.</li>
				<li><strong>iPhone users</strong> : press and hold on the image and you will be prompted to "<strong>Save Image</strong>".</li>
				<li>Do not use <strong>Live-Mode</strong> if your device is slower, the interface may lag.</li>
			</ul>
		</div>
	</div>
</form>