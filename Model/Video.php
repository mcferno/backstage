<?php
/**
 * Video Model : video content represented by URL for streaming videos, or an 
 * uploaded video file. 
 *
 * Online videos are restricted only by the number of adapters written (Youtube, 
 * Vimeo, etc). HTML5 mp4 or webm are supported for local videos, which must be
 * in the correct format, no conversions offered at this time.
 */
class Video extends AppModel {

	public $displayField = 'title';
	public $belongsTo = array('User');
	public $actsAs = array('Postable.Postable' => array(
		'storageModel' => 'Activity'
	));

}