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
		// convert HH:MM:SS duration format to seconds integer
		if(!empty($this->data['Video']['duration']) && strpos($this->data['Video']['duration'], ':') !== false) {
			$this->data['Video']['duration'] = $this->durationToSeconds($this->data['Video']['duration']);
		}

		return parent::beforeSave($options);
	}

	public function convertDate($result) {
		if(!empty($result['Video']['filmed']['month']) && !empty($result['Video']['filmed']['year'])) {
			$date_timestamp = mktime(0, 0, 0, $result['Video']['filmed']['month'], 1, $result['Video']['filmed']['year']);
			$result['Video']['filmed'] = date('Y-m-d', $date_timestamp);
		}
		return $result;
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
	 * Inspects the webroot for a possible preview image to attach to a Video instance
	 *
	 * @param {Video} $video Video object to inspect and attach to
	 */
	public function attachImages(&$video) {
		if(!empty($video['id'])) {
			$image_path = "{$this->thumbnailPath}/{$video['id']}";
			if(file_exists(IMAGES_URL . "{$image_path}.jpg")) {
				$video['thumbnail'] = "{$image_path}.jpg";
			} elseif(file_exists(IMAGES_URL . "{$image_path}.png")) {
				$video['thumbnail'] = "{$image_path}.png";
			}
		}
	}

	/**
	 * Sets an image association with a specific link. Processes uploaded images
	 * to match the proper sizing.
	 */
	public function saveThumbnail($video_id, $crop) {
		if(!class_exists('WideImage')) {
			App::import('Vendor', 'WideImage/WideImage');
		}
		
		$screenshot = $this->thumbnailPath . DS . 'full' . DS . $video_id;
		$thumbnail = $this->thumbnailPath . DS . $video_id;

		if(file_exists(IMAGES_URL . "{$screenshot}.jpg")) {
			$screenshot .= '.jpg';
		} elseif (file_exists(IMAGES_URL . "{$screenshot}.png")) {
			$screenshot .= '.png';
		} else {
			$this->log('File not found for cropping, base: ' . $screenshot);
			return false;
		}
		$image = WideImage::load(IMAGES_URL . $screenshot);
		
		if($image === false) {
			$this->log('Could not open file upload.');
			return false;
		}

		$thumbnail .= '.jpg';

		$cropped = $image->crop($crop['x1'], $crop['y1'], $crop['w'], $crop['h'])->resize($this->thumbnailSize, $this->thumbnailSize);
		$cropped->saveToFile(IMAGES_URL . $thumbnail, 90);
		return true;
	}

	public function humanizeActivity(&$video) {
		$video['Activity']['phrase'] = ":user added a new video";
		if(!empty($video['Link']['title'])) {
			$video['Activity']['phrase'] .= " called \"{$video['Video']['title']}\".";
		}
		$video['Activity']['icon'] = 'film';
		$video['Activity']['link'] = array('controller' => 'videos', 'action' => 'view', $video['Video']['id']);
		if(isset($video['Video']['thumbnail'])) {
			$video['Activity']['preview'] = $video['Activity']['preview-small'] = $video['Video']['thumbnail'];
		}
	}
}