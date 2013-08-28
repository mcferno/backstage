<?php

$this->set(array(
	'contentSpan' => $page['Page']['content_width'],
	'suppressSubnav' => true
));

echo $page['Page']['content'];

if(Access::hasRole('Admin')) : ?>

<hr>
<?= $this->Html->link('Edit this page', array('action' => 'edit', $page['Page']['id']), array('class' => 'btn btn-default')); ?>

<?php endif; ?>