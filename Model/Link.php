<?php
App::uses('AppModel', 'Model');
/**
 * A user owned URL bookmarking and classification system
 */
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

	public function afterFind($results, $primary = false) {

		// singular set
		if(!empty($results['id'])) {
			$this->attachImages($results);

		// multiple set
		} elseif(!empty($results[0]['Link'])) {
			foreach($results as &$result) {
				$this->attachImages($result['Link']);
			}
		}

		return $results;
	}

	/**
	 * Inspects the webroot for a possible preview image to attach to a Link instance
	 *
	 * @param {Link} $link Link object to inspect and attach to
	 */
	public function attachImages(&$link) {
		if(!empty($link['id'])) {
			$image_path = "{$this->thumbnailPath}/{$link['id']}";
			if(file_exists(IMAGES_URL . "{$image_path}.jpg")) {
				$link['thumbnail'] = "{$image_path}.jpg";
			} elseif(file_exists(IMAGES_URL . "{$image_path}.png")) {
				$link['thumbnail'] = "{$image_path}.png";
			}
		}
	}

	public function humanizeActivity(&$link) {
		$link['Activity']['phrase'] = ":user added a new link";
		if(!empty($link['Link']['title'])) {
			$link['Activity']['phrase'] .= " called \"{$link['Link']['title']}\".";
		}
		$link['Activity']['icon'] = 'application-browser';
		$link['Activity']['link'] = array('controller' => 'links', 'action' => 'view', $link['Link']['id']);

		if(isset($link['Link']['thumbnail'])) {
			$link['Activity']['preview'] = $link['Activity']['preview-small'] = $link['Link']['thumbnail'];
		}
	}

	/**
	 * Sets an image association with a specific link. Processes uploaded images
	 * to match the proper sizing.
	 */
	public function saveThumbnail($link_id, $crop) {
		if(!class_exists('WideImage')) {
			App::import('Vendor', 'WideImage/WideImage');
		}

		$screenshot = $this->thumbnailPath . DS . 'full' . DS . $link_id;
		$thumbnail = $this->thumbnailPath . DS . $link_id;

		if(file_exists(IMAGES_URL . "{$screenshot}.jpg")) {
			$screenshot .= '.jpg';
			$thumbnail .= '.jpg';
			$is_jpeg = true;
		} elseif (file_exists(IMAGES_URL . "{$screenshot}.png")) {
			$screenshot .= '.png';
			$thumbnail .= '.png';
			$is_jpeg = false;
		} else {
			$this->log('File not found for cropping, base: ' . $screenshot);
			return false;
		}
		$image = WideImage::load(IMAGES_URL . $screenshot);

		if($image === false) {
			$this->log('Could not open file upload.');
			return false;
		}

		$cropped = $image->crop($crop['x1'], $crop['y1'], $crop['w'], $crop['h'])->resize($this->thumbnailSize, $this->thumbnailSize);
		if($is_jpeg) {
			$cropped->saveToFile(IMAGES_URL . $thumbnail, 90);
		} else {
			$cropped->saveToFile(IMAGES_URL . $thumbnail);
		}
		return true;
	}
}
