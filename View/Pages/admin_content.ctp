<?php

$this->set(array(
	'contentSpan' => $page['Page']['content_width'],
	'suppressSubnav' => true
));

echo $page['Page']['content'];

if($this->Session->read('Auth.User.role') >= ROLES_ADMIN) : ?>

<hr>
<?= $this->Html->link('Edit this page', array('action' => 'edit', $page['Page']['id']), array('class' => 'btn')); ?>

<?php endif; ?>