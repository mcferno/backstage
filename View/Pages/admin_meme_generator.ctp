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
	
	<div class="row">
		<div class="span12">
			<canvas id="workspace" height="450" width="600"></canvas>
		</div>
	</div>
	
	<div class="row">
		<div class="span12">
			<button class="btn btn-success save-image">Save Image</button>
			<button class="btn btn-primary trigger-edit" style="display:none;">Return to Edit Mode</button>
			<div class="save-help alert alert-info" style="display:none;"><strong>Save</strong> the image of your meme above. <strong>iPhone users</strong> : press and hold on the image and you will be prompted to "Save Image".</div>
		</div>
	</div>
</form>