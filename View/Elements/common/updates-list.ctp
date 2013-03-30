<table class="table table-striped activity">
<?php foreach($updates as $update) : ?>
	<tr <?php if(isset($update['Activity']['link'])) { echo 'data-target="' . $this->Html->url($update['Activity']['link']) . '" '; echo 'class="linked"'; } ?>>
		<td class="time extra"><?= date('M d h:i A', strtotime($update['Activity']['created'])); ?></td>
		<td class="type"><?php
			if(isset($update['Activity']['icon'])) {
				echo $this->Html->image("ui/icons/{$update['Activity']['icon']}.png");
			}
		?></td>
		<td>
			<?php if(!empty($update['Activity']['preview-small']) && !isset($hideSmallPreview)) : ?>
			<div class="preview-small"><?= $this->Html->image($update['Activity']['preview-small']); ?></div>
			<?php endif; ?>
		<?php
			$message = $update['Activity']['phrase'];
			if(isset($update['Activity']['link'])) {
				$message = $this->Html->link($message, $update['Activity']['link']);
			}
			if($update['User']['username']) {
				$message = String::insert($message, array('user' => $this->Html->tag('strong', $update['User']['username'])));
			}
			echo $message;
		?>
			<div class="time"><?= $this->Time->timeAgoInWords($update['Activity']['created'], array('end' => '+1 year','accuracy' => array('month' => 'month'))); ?></div>
			<?php if(!empty($update['Activity']['preview'])) : ?>
			<div class="preview" style="display:none;"><?= $this->Html->image($update['Activity']['preview']); ?></div>
			<?php endif; ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<script>
$(document).ready(function() {
	$('.activity tr[data-target]').click(function(){
		window.location = $(this).data('target');
	});
	$('.activity .preview').each(function() {
		$(this).closest('tr').popover({ 
			placement : 'top',
			delay : { show: 250, hide: 100 },
			trigger : 'hover',
			html : true,
			content : $(this).html()
		});
	});
	$('.activity .preview-small').click(function(e) {
		e.preventDefault();
		$(this).closest('tr').popover('toggle');
		return false;
	});
});
</script>