<?php
	$this->Html->script(array('meme-generator.js?t='.filemtime(JS . 'meme-generator.js')), array('inline' => false));

	foreach ($base_images as &$image) {
		$image = $this->Html->webroot(IMAGES_URL . $image);
	}

	$title = 'Meme Generator';
	if(!empty($contest)) {
		$title = 'Caption Battle';
	}
	$this->set('title', $title);

	// javascript app configurations
	$config = array(
		'baseImages' => $base_images,
		'type' => 'Meme'
	);
	if(!empty($contest['Contest']['id'])) {
		$config['type'] = 'Contest';
		$config['contestEntryId'] = $contest['Contest']['id'];
	}
?>
<script>AppEnv['Config']['MemeGenerator'] = <?= json_encode($config); ?>;</script>
<form class="meme-generator">
	<div class="row">
		<div class="col-md-12">
			<h1><?= $title; ?></h1>

			<?php
				if(!empty($contest['User'])) {
					echo $this->Html->tag('p', "Started by <span class=\"glyphicon glyphicon-user\"></span> {$contest['User']['username']}");
				}
				if(!empty($contest['Contest']['message'])) {
					echo $this->Html->tag('p', nl2br($contest['Contest']['message']));
				}
			?>
		</div>
	</div>

	<div class="row no-canvas" style="display:none;">
		<div class="col-md-12">
			<div class="alert alert-danger">
				<h2>Your device does not support HTML5 Canvas!</h2>
				<p>Please consider using a more modern browser, such as <a href="https://www.google.com/chrome" target="_blank">Google Chrome</a>.</p>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<input type="text" name="first-line" id="first-line" class="form-control input-lg" value="<?= $first_line; ?>" placeholder="First line of text" spellcheck="true" autofocus="true">
			<input type="text" name="last-line" id="last-line" class="form-control input-lg" value="<?= $last_line; ?>" placeholder="Last line of text" spellcheck="true">
		</div>
	</div>

	<div class="row actions">
		<div class="col-md-12">
			<div class="btn-group">
				<button class="btn btn-huge btn-primary save-image" title="Refreshes the text on top of the image below"><span class="glyphicon glyphicon-refresh"></span> Refresh<span class="extra"> Image</span></button>
				<button class="btn btn-huge btn-inverse live-mode" title="Toggles the automatic text refreshing mode"><span class="glyphicon glyphicon-remove"></span> Auto<span class="extra"> Refresh</span></button>
			</div>

			<button class="btn btn-huge btn-default choose-background" data-loading-text='<span class="glyphicon glyphicon-refresh"></span><span class="extra"> Change Image</span>'><span class="glyphicon glyphicon-picture"></span><span class="extra"> Change Image</span></button>

			<?php
				if(!empty($contest['Contest']['id'])) {
					echo $this->Html->link('<span class="glyphicon glyphicon-search"></span> View Battle', array('controller' => 'contests', 'action' => 'view', $contest['Contest']['id']), array('class' => 'btn btn-default', 'escape' => false));
				}
			?>
		</div>
	</div>

	<div class="row output workspace" style="display:none;">
		<div class="col-md-12">
			<canvas id="rasterizer" height="450" width="600" style="display:none;"></canvas>
		</div>
	</div>

	<?php if(empty($base_images)) : ?>
	<div class="row" id="backgrounds" style="display:none;">
		<div class="col-md-12">
			<h2 class="cozy-top">Choose an image</h2>

			<div class="row">
				<div class="col-md-8 col-md-offset-2">
					<div class="row">
						<div class="col-md-6">
							<?= $this->Form->input('image_tags', array('type' => 'select', 'options' => $image_tags, 'empty' => 'All Images', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
						</div>
						<div class="col-md-6">
							<?= $this->Form->input('image_owners', array('type' => 'select', 'options' => $image_owners, 'empty' => 'All Users', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="mini-wall clearfix"></div>
			<button class="load-more btn btn-primary"><span class="glyphicon glyphicon-retweet"></span> <span class="extra">Load </span>More Images</button>
		</div>
	</div>
	<?php endif; ?>

	<script type="text/template" id="imagePickerTemplate">
	<img src="<%= thumb_url %>" data-full-image="<%= full_url %>" class="image-option">
	</script>

	<div class="row workspace" style="display:none;">
		<div class="col-md-12">
			<div class="btn-group">
				<?php if(!empty($contest['Contest']['id'])) : ?>
				<button class="btn btn-huge btn-success save" data-loading-text='<span class="glyphicon glyphicon-download-alt"></span> Saving ...' title="Save this entry, and stay in the Meme Generator"><span class="glyphicon glyphicon-download"></span> Save Entry<span class="extra"> &amp; Continue</span></button>
				<?php else : ?>
				<button class="btn btn-huge btn-success save" data-loading-text='<span class="glyphicon glyphicon-download-alt"></span> Saving ...' title="Save this image, and stay in the Meme Generator"><span class="glyphicon glyphicon-download"></span> Save<span class="extra"> &amp; Continue</span></button>
				<?php endif; ?>
				<button class="btn btn-huge btn-default save-jump" data-loading-text='<span class="glyphicon glyphicon-download-alt"></span> Saving ...' title="Save Image and Jump to View Page"><span class="glyphicon glyphicon-eye-open"></span> Save &amp; View</button>
			</div>
			<a href="#" style="display:none;" class="btn btn-huge btn-info view-last" title="View the last image you saved"><span class="glyphicon glyphicon-search"></span> View Last Saved</a>
		</div>
	</div>
	<div class="row resize-reset workspace" style="display:none;">
		<div class="col-md-12">
			<select name="canvasSize" class="canvasSize input-sm" title="Change the size of this meme when saved">
				<option data-max="full">Image Size: Full</option>
				<option data-max="800">Image Size: Large</option>
				<option data-max="600" selected="selected">Image Size: Regular</option>
			</select>

			<button class="btn btn-sm btn-danger reset" title="Abandon your work and restart"><span class="glyphicon glyphicon-ban-circle"></span> Start Over</button>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<ul class="list-unstyled tips">
				<li>If you "<strong>Save on Server</strong>", it will automatically appear in "<strong>My Images</strong>" afterwards.</li>
				<li><strong>iPhone users</strong> : To save a meme on your phone, press and hold on the image and you will be prompted to "<strong>Save Image</strong>".</li>
				<li>Disable <strong>Auto-Refresh</strong> if your device is slower, and the interface is lagging.</li>
			</ul>
		</div>
	</div>
</form>