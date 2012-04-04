<?php 
	$this->set('suppressSubnav', true); 
	$this->Html->script(array('meme-generator'),false);
?>
<h1>Meme Generator</h1>
<form class="meme-generator">
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
			<button class="btn btn-inverse live-mode"><i class="icon-white icon-remove"></i> Live-Mode</button>
		</div>
	</div>
	
	<div class="row output">
		<div class="span12">
			<canvas id="workspace" height="450" width="600"></canvas>
		</div>
	</div>
	<div class="row">
		<div class="span12">
			<h3>Image Size</h3>
			<select name="canvasSize" class="canvasSize">
				<option data-width="600" data-height="450" selected="selected">Regular (600 x 450)</option>
				<option data-width="800" data-height="600">Large (800 x 600)</option>
			</select>
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