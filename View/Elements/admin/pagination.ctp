<?php

// accept page_limit options, or use default set
$page_limits = (isset($page_limits)) ? $page_limits : array(16, 48, 96);

if(isset($show_summary) && $show_summary) :
?>
<p class="paging-summary">
	<?= $this->Paginator->counter(array('format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total.'))); ?>
</p>
<?php endif; // summary ?>

<div class="paging">
	<ul class="pager">
		<li class=""><?= $this->Paginator->prev('<span class="glyphicon glyphicon-chevron-left"></span> previous', array('escape'=>false), null, array('escape'=>false, 'class' => 'prev disabled')); ?></li>
		<li class=""><?= $this->Paginator->numbers(array('separator' => '', 'modulus'=>4)); ?></li>
		
		<?php if($page_limits !== false) : ?>
		
		<li class="dropdown page-limit">
			<a href="#"  role="button" data-target="#" class="dropdown-toggle"  data-toggle="dropdown"><?= $this->params['paging'][$this->Paginator->defaultModel()]['limit']; ?> per page</a>
			<ul class="dropdown-menu">
			<?php 
				foreach ($page_limits as $limit) {
					echo $this->Html->tag('li', $this->Paginator->link($limit, array('limit' => $limit, 'page' => 1)));
				}
			?>
			</ul>
		</li>
		
		<?php endif; // show page limit links ?>
		
		<li class=""><?= $this->Paginator->next('next <span class="glyphicon glyphicon-chevron-right"></span>', array('escape'=>false,'class'=>''), null, array('escape'=>false,'class' => 'next disabled')); ?></li>
	</ul>
</div>