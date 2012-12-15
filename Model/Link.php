<?php
App::uses('AppModel', 'Model');

class Link extends AppModel {

	public $displayField = 'title';
	public $order = array('Link.created' => 'DESC');

	public $belongsTo = array('User');
	public $actsAs = array(
		'Taggable', 
		'Ownable',
		'Postable.Postable' => array(
			'storageModel' => 'Activity'
		)
	);
	public $hasAndBelongsToMany = array(
		'Tag' => array(
			'joinTable' => 'taggings',
			'foreignKey' => 'foreign_id'
		)
	);

	public $thumbnailSize = 150;
	public $thumbnailPath = 'user/links';

	public function humanizeActivity(&$link) {
		$link['Activity']['phrase'] = ":user added a new link";
		if(!empty($link['Link']['title'])) {
			$link['Activity']['phrase'] .= " called \"{$link['Link']['title']}\".";
		}
		$link['Activity']['icon'] = 'application-browser';
		$link['Activity']['link'] = array('controller' => 'links', 'action' => 'view', $link['Link']['id']);
	}

	/**
	 * Sets an image association with a specific link. Processes uploaded images
	 * to match the proper sizing.
	 */
	public function attachImage($link_id, $file_path) {
		App::import('Vendor', 'WideImage/WideImage');
		
		$image = WideImage::load($file_path);
		
		if($image === false) {
			$this->log('Could not open file upload.');
			return false;
		}

		$cropped = $image->resize($this->thumbnailSize, $this->thumbnailSize);
		$cropped->saveToFile($new_path, $this->jpegQuality);
	}
}
