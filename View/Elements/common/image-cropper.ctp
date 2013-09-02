<?php
/**
 * Image cropping tools and library (single use only)
 * Pull in immediately above the image which is to be cropped.
 */
$this->Html->script('https://cdnjs.cloudflare.com/ajax/libs/jquery-jcrop/0.9.12/js/jquery.Jcrop.min.js', array('inline' => false));
$this->Html->css('https://cdnjs.cloudflare.com/ajax/libs/jquery-jcrop/0.9.12/css/jquery.Jcrop.min.css', null, array('inline' => false));
?>
<script>Backstage.cropUrl = <?= json_encode($this->Html->url(array('action' => 'crop'))); ?>;</script>
<div class="crop-actions" style="display: none;">
	<h3 class="pull-right">New Size &nbsp;<span class="badge badge-info crop-width">0</span> x <span class="badge badge-info crop-height">0</span> px</h3>
	<button class="btn btn-primary crop-save"><span class="glyphicon glyphicon-download"></span> Save Cropped Image</button>
	<button class="btn btn-default crop-cancel"><span class="glyphicon glyphicon-remove"></span> Cancel</button>
</div>