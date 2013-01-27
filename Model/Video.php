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

	public $thumbnailSize = 300;
	public $thumbnailPath = 'user/videos';

	public function beforeSave($options = array()) {
		parent::beforeSave($options);

		// convert HH:MM:SS duration format to seconds integer
		if(!empty($this->data['Video']['duration']) && strpos($this->data['Video']['duration'], ':') !== false) {
			$this->data['Video']['duration'] = $this->durationToSeconds($this->data['Video']['duration']);
		}

		return true;
	}

	public function afterFind($results, $primary = false) {
		if(isset($results[0]['Video']['duration'])) {
			foreach ($results as &$result) {
				$result['Video']['duration_nice'] = $this->secondsToDuration($result['Video']['duration']);
			}
		}

		// singular set
		if(!empty($results['id'])) {
			$this->attachImages($results);

		// multiple set
		} elseif(!empty($results[0]['Video'])) {
			foreach($results as &$result) {
				$this->attachImages($result['Video']);
			}
		}

		return $results;
	}

	/**
	 * Converts a duration string HH:MM:SS, MM:SS, or SS to its seconds equivalent
	 */
	public function durationToSeconds($string) {
		$parts = explode(':', $string);
		$count = count($parts);
		$total = 0;
		foreach($parts as $key => $value) {
			$total += $value * pow(60, $count - $key - 1);
		}
		return $total;
	}

	/**
	 * Converts a seconds integer to its duration syntax (HH:MM:SS)
	 *
	 * @param {Integer} $integer Seconds to convert to duration, 24 hr limit
	 * @return {String} Duration string expressed as HH:MM:SS
	 */
	public function secondsToDuration($integer) {
		return gmdate("H:i:s", $integer);
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