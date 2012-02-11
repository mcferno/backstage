<?php 

App::uses('Helper', 'AppHelper');

class SiteHelper extends AppHelper {
	
	public $helpers = array('Html');
	
	/**
	 * Inspects the Post Model data to determine the most appropriate profile
	 * image.
	 *
	 * @param {Array} $data Post Model data
	 * @return {String} Image HTML
	 */
	public function profileImage($data) {
		if(!empty($data['Post']['model'])) {
			if($data['Post']['model'] == 'Tumblr') {
				if(stripos($data['Post']['source'],'guest') !== false) {
					return $this->Html->image('profile/guest-profile96.jpg',array('alt'=>'Guest','title'=>'Guest'));
				} else {
					return $this->Html->image('profile/kqm-profile96.jpg',array('alt'=>'The Man Himself','title'=>'The Man Himself'));
				}
			} elseif($data['Post']['model'] == 'Twitter') {
				$image = $this->Html->image($this->_View->viewVars['accounts']['fakeclouds'],array('width'=>96,'height'=>96,'alt'=>'@fakeclouds','title'=>'@fakeclouds'));
				if(!empty($data['Post']['permalink'])) {
					return $this->Html->link($image,$data['Post']['permalink'],array('escape'=>false));
				} else {
					return $image;
				}
			}
		}
	}
}