<?php
	if($this->params->paging['Post']['page'] != 1) {
		$breadcrumbs[] = array(
			'title'=>"Page {$this->params->paging['Post']['page']}",
			'url'=>$this->params->here
		);
		$this->set('breadcrumbs',$breadcrumbs);
		$this->set('page_title',"Page {$this->params->paging['Post']['page']} - Posts");
	}
?>
<nav class="browse clearfix">
	<div class="pagination">
		<?= $this->Paginator->prev('&larr; Newer',array('escape'=>false),null,array('class'=>'hide')); ?>
		<?= $this->Paginator->next('Older &rarr;',array('escape'=>false)); ?>
	</div>
</nav>

<?php foreach($posts as $post) : $stripe = 0; ?>
<article class="post posttype-quote <?= (($stripe++ % 2) == 0)?'even':'odd'; ?>">
<?= $this->element('post-body',array('post'=>$post)); ?>
</article><!-- .post -->
<?php endforeach; ?>

<nav class="browse clearfix">
	<div class="pagination">
		<?= $this->Paginator->prev('&larr; Newer',array('escape'=>false),null,array('class'=>'hide')); ?>
		<?= $this->Paginator->next('Older &rarr;',array('escape'=>false)); ?>
	</div>
</nav>