<?php

$removeFields = array('id');
foreach ($schema as $name => $info) {
	if($info['type'] == 'text') {
		$removeFields[] = $name;
	}
}
$scaffoldFields = array_diff($scaffoldFields, $removeFields);



?>
<div class="<?php echo $pluralVar; ?> index">
<h2><?php echo $pluralHumanName; ?></h2>

<?= $this->element('admin/pagination'); ?>

<table class="table table-condensed table-striped">
<tr>
<?php foreach ($scaffoldFields as $_field): ?>
	<th><?php echo $this->Paginator->sort($_field); ?></th>
<?php endforeach; ?>
	<th><?php echo __d('cake', 'Actions'); ?></th>
</tr>
<?php
$i = 0;
foreach (${$pluralVar} as ${$singularVar}):
	echo "<tr>";
		foreach ($scaffoldFields as $_field) {
			$isKey = false;
			if (!empty($associations['belongsTo'])) {
				foreach ($associations['belongsTo'] as $_alias => $_details) {
					if ($_field === $_details['foreignKey']) {
						$isKey = true;
						echo "<td>" . $this->Html->link(${$singularVar}[$_alias][$_details['displayField']], array('controller' => $_details['controller'], 'action' => 'view', ${$singularVar}[$_alias][$_details['primaryKey']])) . "</td>";
						break;
					}
				}
			}
			if ($isKey !== true) {
				echo "<td>" . h(${$singularVar}[$modelClass][$_field]) . "</td>";
			}
		}

		echo '<td class="actions">';
		echo $this->Html->link(__d('cake', 'View'), array('action' => 'view', ${$singularVar}[$modelClass][$primaryKey]));
		echo $this->Html->link(__d('cake', 'Edit'), array('action' => 'edit', ${$singularVar}[$modelClass][$primaryKey]));
		echo $this->Form->postLink(
			__d('cake', 'Delete'),
			array('action' => 'delete', ${$singularVar}[$modelClass][$primaryKey]),
			null,
			__d('cake', 'Are you sure you want to delete').' #' . ${$singularVar}[$modelClass][$primaryKey]
		);
		echo '</td>';
	echo '</tr>';

endforeach;

?>
</table>

<?= $this->element('admin/pagination', array('show_summary' => true)); ?>

</div>
<div class="actions">
		<?php echo $this->Html->link(__d('cake', 'New %s', $singularHumanName), array('action' => 'add'), array('class' => 'btn')); ?>
<?php
		$done = array();
		foreach ($associations as $_type => $_data) {
			foreach ($_data as $_alias => $_details) {
				if ($_details['controller'] != $this->name && !in_array($_details['controller'], $done)) {
					echo $this->Html->link(__d('cake', 'List %s', Inflector::humanize($_details['controller'])), array('controller' => $_details['controller'], 'action' => 'index'), array('class' => 'btn'));
					echo $this->Html->link(__d('cake', 'New %s', Inflector::humanize(Inflector::underscore($_alias))), array('controller' => $_details['controller'], 'action' => 'add'), array('class' => 'btn'));
					$done[] = $_details['controller'];
				}
			}
		}
?>
</div>
