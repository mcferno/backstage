<?php
App::uses('Folder', 'Utility');
class Asset extends AppModel {
	
	public $displayField = 'filename';
	public $belongsTo = array('User');
	public $hasOne = array(
		'ContestWin' => array(
			'className' => 'Contest',
			'foreignKey' => 'winning_asset_id'
	));
	public $hasMany = array('Contest');
	
	// recognized dataURL image types
	public $headers = array(
		'jpg' => 'data:image/jpeg;base64,'
	);
	
	public $maxDimensions = array(
		'w' => 1200,
		'h' => 1200
	);
	
	// thumbnail generation size widths
	public $imageThumbs = array(75, 200);
	
	// default quality (out of 100) for image transformations
	public $jpegQuality = 85;
	
	public function __construct() {
		parent::__construct();
		$this->folderPathRelative = 'user' . DS;
		$this->folderPath = WWW_ROOT . 'img' . DS . $this->folderPathRelative;
	}
	
	/**
	 * Retrieve the images within the user's site folder
	 *
	 * @param {String} $cluster Folder name or relative path
	 * @return {Array} Image paths
	 */
	public function getImages($cluster = false) {
		return glob($this->getFolderPath($cluster).'*.*');
	}
	
	public function getFolderPath($cluster = false) {
		$path = $this->folderPath;
		if($cluster !== false) {
			$path .= $cluster . DS;
		}
		return $path;
	}

	/**
	 * Determins the server path to a specific asset
	 *
	 * @param {UUID} $asset_id
	 * @return {String} Server path, relative to the weboot image folder
	 */
	public function getPath($asset_id, $size = false) {
		
		$asset = $this->findById($asset_id);
		
		if(!empty($asset['Asset']['filename'])) {
			
			$base = $this->folderPathRelative . $asset['Asset']['user_id'] . DS;
			if($size) {
				$base .= $size . DS;
			}
			return $base . $asset['Asset']['filename'];
		}
		
		return '';
	}
	
	/**
	 * Saves and processes an base64 encoded image.
	 *
	 * @param {String} $data Base64 encoded image data
	 * @param {UUID} $user_id User ownership
	 * @param {String} $type String classification
	 * @return {Boolean}
	 */
	public function saveEncodedImage(&$data, $user_id, $type = 'Image') {
		
		// skim the header data to avoid searching over large data strings
		$header = substr($data,0,30);
		
		if(stripos($header,$this->headers['jpg']) !== false) {
			
			$image_name = String::uuid().'.jpg';
			$folder = $this->folderPath . $user_id . DS;
			if(!file_exists($folder)) {
				$dir = new Folder($folder, true, 0755);
			}
			$new_path =  $folder . $image_name;
			
			$new_image = fopen($new_path,'w');
			if($new_image === false) {
				$this->log("Can't open {$new_path} for writing.");
				return false;
			}
			
			$image_data = base64_decode(substr($data,strlen($this->headers['jpg'])));
			$write_status = fwrite($new_image,$image_data);
			fclose($new_image);
			if($write_status === false) {
				$this->log("Can't write image data into {$new_path}.");
				return false;
			}
			
			$data = array(
				'type' => $type,
				'filename' => $image_name,
				'ext' => 'jpg',
				'checksum' => sha1($image_data),
				'user_id' => $user_id
			);
			
			$save_status = $this->save($data);
			if($write_status === false) {
				$this->log("Can't record image {$new_path} meta to db.");
				return false;
			}
			
			// process image into multiple subsizes
			$this->saveThumbs($new_path);
			return true;
		}
		
		$this->log("No asset handler found.");
		return false;
	}
	
	/**
	 * Saves and processes a file upload.
	 *
	 * @param {String} $file_path System path to image source
	 * @param {UUID} $user_id User ownership
	 * @param {String} $type String classification
	 * @return {Boolean}
	 */
	public function saveImage($file_path, $user_id, $type = 'Image') {
		App::import('Vendor', 'WideImage/WideImage');
		
		$image = WideImage::load($file_path);
		
		if($image === false) {
			$this->log('Could not open file upload.');
			return false;
		}
		
		$image_name = String::uuid().'.jpg';
		$folder = $this->folderPath . $user_id . DS;
		if(!file_exists($folder)) {
			$dir = new Folder($folder, true, 0755);
		}
		$new_path =  $folder . $image_name;
		
		$cropped = $image->resize($this->maxDimensions['w'],$this->maxDimensions['h']);
		$cropped->saveToFile($new_path, $this->jpegQuality);
		
		$data = array(
			'type' => $type,
			'filename' => $image_name,
			'ext' => 'jpg',
			'checksum' => -1,
			'user_id' => $user_id
		);
		
		$save_status = $this->save($data);
		if($save_status === false) {
			$this->log("Can't record image {$new_path} meta to db.");
			return false;
		}
		
		// process image into multiple subsizes
		$this->saveThumbs($new_path);
		return $this->id;
	}
	
	/**
	 * Resizes an image on disk to 
	 *
	 * @param {String} $imagePath Path to image to process
	 */
	public function saveThumbs($imagePath) {
		App::import('Vendor', 'WideImage/WideImage');
				
		$image = WideImage::load($imagePath);
		
		if($image === false) {
			$this->log('Could not open original image for thumb generation');
			return false;
		}
		
		$base_path = dirname($imagePath).DS;
		$filename = substr($imagePath,strlen($base_path));
				
		foreach($this->imageThumbs as $width) {
			$cropped = $image->resize($width,$width);
			
			$folder = $base_path . DS . $width . DS;
			if(!file_exists($folder)) {
				$dir = new Folder($folder, true, 0755);
				unset($dir);
			}
			
			$cropped->saveToFile($folder . $filename, $this->jpegQuality);
			
			unset($cropped);
		}
		return true;
	}
	
	/**
	 * Prepare a single asset to post to Facebook
	 *
	 * @param {UUID} $asset_id Asset primary key to post
	 * @return {Array}
	 */
	public function castToFacebook($asset_id) {
		$asset = $this->findById($asset_id);
		if(!empty($asset)) {
			return array(
				'source' => '@' . $this->folderPath . $asset['Asset']['user_id'] . DS . $asset['Asset']['filename']
			);
		}
		return false;
	}
	
	/**
	 * Clean up files before a record is deleted
	 *
	 * @param {Boolean} $cascade
	 * @return {Boolean}
	 */
	public function beforeDelete($cascade) {
		$res = parent::beforeDelete($cascade);
		if($res) {
			$record = $this->findById($this->id);
			$base_path = "{$this->folderPath}{$record['Asset']['user_id']}/";
			
			if(!empty($record)) {
				foreach($this->imageThumbs as $width) {
					$path = "{$base_path}{$width}/{$record['Asset']['filename']}";
					if(file_exists($path)) {
						unlink($path);
					}
				}
				unlink("{$base_path}{$record['Asset']['filename']}");
			}
			return true;
		}
		return false;
	}
}