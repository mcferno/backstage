<?php

$this->Html->script(array(
	'/lib/select2-3.2/select2.min.js',
	'tagging.js?t=' . filemtime(JS . 'tagging.js')
), array('inline' => false));

$this->Html->css('/lib/select2-3.2/select2.css', null, array('inline' => false));

?>

<h5>Quick Tagging</h5>
<input class="quick-tagger form-control" type="text" value="">
<button class="btn btn-sm save-quick-tags">Save</button>