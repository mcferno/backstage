<?php
$this->Html->script('/lib/select2-3.2/select2.min.js', array('inline' => false));
$this->Html->css('/lib/select2-3.2/select2.css', null, array('inline' => false));

$this->Form->unlockField('Tagging.tags');
echo $this->Form->input('Tagging.tags', array('type' => 'hidden', 'class' => 'content-tags'));
echo $this->Form->input('Tagging.model', array('type' => 'hidden', 'value' => $model));
echo $this->Form->input('Tagging.foreign_id', array('type' => 'hidden', 'value' => isset($foreign_key) ? $foreign_key : ''));
echo $this->Form->input('Tagging.user_id', array('type' => 'hidden', 'value' => $this->Session->read('Auth.User.id')));
?>
<script type="text/javascript">
Backstage.selectTags = <?= json_encode($tags); ?>;
</script>