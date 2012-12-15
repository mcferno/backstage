<?php
/**
 * Image cropping tools and library (single use only)
 * Pull in immediately above the image which is to be cropped.
 */
$this->Html->script('//cdnjs.cloudflare.com/ajax/libs/jquery-jcrop/0.9.10/jquery.Jcrop.js', array('inline' => false));
$this->Html->css('/lib/jcrop/jquery.Jcrop.min.css', null, array('inline' => false));
?>
<script>Backstage.cropUrl = <?= json_encode($this->Html->url(array('action' => 'crop'))); ?>;</script>
<div class="crop-actions" style="display: none;">
	<h3 class="pull-right">New Size &nbsp;<span class="badge badge-info crop-width">0</span> x <span class="badge badge-info crop-height">0</span> px</h3>
	<button class="btn btn-large btn-primary crop-save"><i class="icon-white icon-download"></i> Save Cropped Image</button>
	<button class="btn btn-large btn crop-cancel"><i class="icon icon-remove"></i> Cancel</button>
</div>