<table class="table table-striped activity activity-expanded boxed-table">
	<tbody>
	<?php foreach($updates as $update) : ?>
		<tr <?php if(isset($update['Activity']['link'])) { echo 'data-target="' . $this->Html->url($update['Activity']['link']) . '" '; echo 'class="linked"'; } ?>>
			<td class="preview">
				<?php if(!empty($update['Activity']['preview']) && !isset($hideSmallPreview)) : ?>
					<div class="preview-small"><?= $this->Html->image($update['Activity']['preview']); ?></div>
				<?php endif; ?>
			</td>
			<?php if(isset($update['Activity']['icon'])) : ?>
			<td class="type">
				<div class="icon-row" style="background-image: url('<?= $this->Html->webroot(IMAGES_URL . "ui/icons/{$update['Activity']['icon']}.png"); ?>')"></div>
			</td>
			<?php endif; ?>
			<td class="specs">
				<?php
				$message = $update['Activity']['phrase'];
				if(isset($update['Activity']['link'])) {
					$message = $this->Html->link($message, $update['Activity']['link']);
				}
				if($update['User']['username']) {
					$message = CakeText::insert($message, array('user' => $this->Html->tag('strong', $update['User']['username'])));
				}
				echo $message;
				?>
				<div class="time">
					<?php
						$timeFormat = 'M j Y, g:i A';
						$showTimeAgo = (strtotime($update['Activity']['created']) > strtotime('now - 1 year'));

						// activity is from the same as the current year
						if (strtotime($update['Activity']['created']) > mktime(0,0,0,1,1)) {
							$timeFormat = 'M jS, g:i A';
						}
					?>
					<?= date($timeFormat, strtotime($update['Activity']['created'])); ?>
					<?php if ($showTimeAgo) : ?>
					<small>(<?= $this->Time->timeAgoInWords($update['Activity']['created'], array('end' => '+1 year', 'accuracy' => array('month' => 'month'))); ?>)</small>
					<?php endif; // time ago in words ?>
				</div>
				<?php if(!empty($update['Activity']['preview'])) : ?>
					<div class="preview" style="display:none;"><?= $this->Html->image($update['Activity']['preview']); ?></div>
				<?php endif; ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<?php $this->append('scriptBlock'); ?>
<script>
	$(document).ready(function() {
		$('.activity tr[data-target]').click(function(){
			window.location = $(this).data('target');
		});
	});
</script>
<?php $this->end(); ?>
