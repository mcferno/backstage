<div class="link-exchange link-view">
	<?= $this->element('../Links/_link_item', array('link' => $link)); ?>
</div>

<h3 class="text-right"><?= $this->Html->image('ui/icons/balloon.png'); ?> Comments</h3>
<?= $this->element('common/chat-module', array('model' => 'Link', 'foreign_key' => $link['Link']['id'])); ?>