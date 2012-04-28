<?php if(isset($show_summary) && $show_summary) : ?>
<p class="paging-summary">
<?= $this->Paginator->counter(array('format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}'))); ?>
</p>
<?php endif; // summary ?>
<div class="paging">
	<ul class="pager">
		<li class=""><?= $this->Paginator->prev('<i class="icon icon-chevron-left"></i> previous', array('escape'=>false), null, array('escape'=>false, 'class' => 'prev disabled')); ?></li>
		<li class=""><?= $this->Paginator->numbers(array('separator' => '', 'modulus'=>4)); ?></li>
		<li class=""><?= $this->Paginator->next('next <i class="icon icon-chevron-right"></i>', array('escape'=>false,'class'=>''), null, array('escape'=>false,'class' => 'next disabled')); ?></li>
	</ul>
</div>