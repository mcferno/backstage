<?php
	$breadcrumbs[] = array(
		'title' => $this->Text->truncate($post['Post']['body'],45),
		'url' => $this->params->here
	);
	$this->set('breadcrumbs', $breadcrumbs);
	$this->set('page_title', 'Post');

	$meta_desc = $post['Post']['body'];
	if(!empty($post['Post']['source'])) {
		$meta_desc .= ' - ' .$post['Post']['source'];
	}
	$this->set('meta_description', $meta_desc);
?>

<article class="post posttype-quote focus">
<?= $this->element('post-body', array('post' => $post)); ?>
</article>
