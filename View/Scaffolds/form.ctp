<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Scaffolds
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<div class="<?php echo $pluralVar; ?> form">
<?php
	echo $this->Form->create();
	$fields = array_diff($scaffoldFields, array('created', 'modified', 'updated'));
	foreach($fields as $field) {
		echo $this->Form->input($field, array('class' => 'form-control'));
	}
	echo $this->Form->end(__d('cake', 'Submit'));
?>
</div>
<div class="actions">
<?php if ($this->request->action != 'add'): ?>
		<?php echo $this->Form->postLink(
			__d('cake', 'Delete'),
			array('action' => 'delete', $this->Form->value($modelClass . '.' . $primaryKey)),
			array('class' => 'btn btn-danger'),
			__d('cake', 'Are you sure you want to delete # %s?', $this->Form->value($modelClass . '.' . $primaryKey)));
		?>
<?php endif; ?>
		<?php echo $this->Html->link(__d('cake', 'List') . ' ' . $pluralHumanName, array('action' => 'index'), array('class' => 'btn btn-default')); ?>
<?php
		$done = array();
		foreach ($associations as $_type => $_data) {
			foreach ($_data as $_alias => $_details) {
				if ($_details['controller'] != $this->name && !in_array($_details['controller'], $done)) {
					echo $this->Html->link(__d('cake', 'List %s', Inflector::humanize($_details['controller'])), array('controller' => $_details['controller'], 'action' => 'index'), array('class' => 'btn btn-default'));
					echo $this->Html->link(__d('cake', 'New %s', Inflector::humanize(Inflector::underscore($_alias))), array('controller' => $_details['controller'], 'action' => 'add'), array('class' => 'btn btn-default'));
					$done[] = $_details['controller'];
				}
			}
		}
?>
</div>