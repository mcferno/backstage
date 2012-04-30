<?php

class Asset extends AppModel {
	
	public $displayField = 'filename';
	
	// recognized dataURL image types
	public $headers = array(
		'jpg' => 'data:image/jpeg;base64,'
	);
	
	// thumbnail generation size widths
	public $imageThumbs = array(75, 200);
	
	// default quality (out of 100) for image transformations
	public $jpegQuality = 70;
	
	public function __construct() {
		parent::__construct();
		$this->folderPathRelative = 'img'.DS.'user'.DS;
		$this->folderPath = WWW_ROOT . $this->folderPathRelative;
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
	
	public function saveImage(&$data, $user_id) {
		
		// skim the header data to avoid searching over large data strings
		$header = substr($data,0,30);
		
		if(stripos($header,$this->headers['jpg']) !== false) {
			
			$image_name = String::uuid().'.jpg';
			$new_path = $this->folderPath . $user_id . DS . $image_name;
			
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
				'type'=>'Image',
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
			$cropped->saveToFile($base_path . DS . $width . DS . $filename, $this->jpegQuality);
		}
		return true;
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