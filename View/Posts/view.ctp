<?php
	$breadcrumbs[] = array(
		'title'=>$this->Text->truncate($post['Post']['body'],45),
		'url'=>$this->params->here
	);
	$this->set('breadcrumbs',$breadcrumbs);
?>

<article class="post posttype-quote focus">
<?= $this->element('../posts/_body',array('post'=>$post)); ?>
</article>
