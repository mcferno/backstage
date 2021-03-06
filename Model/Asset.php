<?php
App::uses('Folder', 'Utility');
use WideImage\WideImage;

/**
 * User-generated media, primarily in the form of images. Can be uploaded content
 * or media scraped from the web.
 */
class Asset extends AppModel
{
	public $displayField = 'filename';
	public $belongsTo = array(
		// owner of the image, video or etc.
		'User',

		// optional grouping of media into albums
		'Album'
	);
	public $hasOne = array(
		// Asset is chosen as the winning entry of a caption Contest.
		'ContestWin' => array(
			'className' => 'Contest',
			'foreignKey' => 'winning_asset_id'
		)
	);
	public $hasMany = array(
		// Asset is the subject of one or more caption Contests
		'Contest'
	);
	public $hasAndBelongsToMany = array(
		// Asset classification by a set of subject tags
		'Tag' => array(
			'joinTable' => 'taggings',
			'foreignKey' => 'foreign_id'
		),
		'ContestEntry' => array(
			'className' => 'Contest'
		)
	);

	public $actsAs = array(
		'Postable.Postable' => array(
			'storageModel' => 'Activity',
			'inclusionCallback' => 'notificationInclusion'
		),
		'Ownable',
		'Taggable'
	);

	// recognized dataURL image types
	public $headers = array(
		'jpg' => 'data:image/jpeg;base64,'
	);

	// configurable max and min size of images (in px)
	public $minDimensions = null;
	public $maxDimensions = null;

	// thumbnail generation size widths (in px)
	public $imageThumbs = array(75, 200);

	// default quality (out of 100) for image transformations
	public $jpegQuality = 90;

	public function __construct()
	{
		parent::__construct();
		$this->folderPathRelative = 'user' . DS;
		$this->folderPath = WWW_ROOT . 'img' . DS . $this->folderPathRelative;

		$this->minDimensions = Configure::read('Site.Images.minDimension');
		$this->maxDimensions = Configure::read('Site.Images.maxDimension');
	}

	/**
	 * Attach useful meta-data after retrieval
	 */
	public function afterFind($results, $primary = false)
	{
		// multi-array format
		if(isset($results[0]['Asset'])) {
			foreach($results as &$result) {
				$this->addMetaData($result['Asset']);
			}

		} else if(isset($results[0]['Cover'])) {
			foreach($results as &$result) {
				$this->addMetaData($result['Cover']);
			}

		// single direct result format
		} else if(isset($results['id'])) {
			$this->addMetaData($results);

		// hasMany
		} else if(isset($results[0]['id'])) {
			foreach($results as &$result) {
				$this->addMetaData($result);
			}
		}

		return $results;
	}

	/**
	 * Adds meta-data information to a single Asset record.
	 *
	 * @param &Asset Direct Asset reference to attach meta-data
	 */
	public function addMetaData(&$result)
	{
		// add image references
		if(!empty($result['filename']) && !empty($result['user_id'])) {
			$base = "{$this->folderPathRelative}{$result['user_id']}";

			$result['image-full'] = "{$base}/{$result['filename']}";
			$result['image-thumb'] = "{$base}/200/{$result['filename']}";
			$result['image-tiny'] = "{$base}/75/{$result['filename']}";
		}
	}

	/**
	 * Retrieve the images within the user's site folder
	 *
	 * @param string $cluster Folder name or relative path
	 * @return array Image paths
	 */
	public function getImages($cluster = false)
	{
		return glob($this->getFolderPath($cluster) . '*.*');
	}

	public function getFolderPath($cluster = false)
	{
		$path = $this->folderPath;
		if($cluster !== false) {
			$path .= $cluster . DS;
		}
		return $path;
	}

	/**
	 * Determines the server path to a specific asset
	 *
	 * @param string $asset_id
	 * @return string Server path, relative to the weboot image folder
	 */
	public function getPath($asset_id, $size = false)
	{
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
	 * @param string $data Base64 encoded image data
	 * @param string $user_id User ownership
	 * @param string $type String classification
	 * @return boolean
	 */
	public function saveEncodedImage(&$data, $user_id, $type = 'Image')
	{
		// skim the header data to avoid searching over large data strings
		$header = substr($data, 0, 30);

		if(stripos($header, $this->headers['jpg']) !== false) {

			$image_name = CakeText::uuid() . '.jpg';
			$folder = $this->folderPath . $user_id . DS;
			if(!file_exists($folder)) {
				$dir = new Folder($folder, true, 0755);
			}
			$new_path = $folder . $image_name;

			$new_image = fopen($new_path, 'w');
			if($new_image === false) {
				$this->log("Can't open {$new_path} for writing.");
				return false;
			}

			$image_data = base64_decode(substr($data, strlen($this->headers['jpg'])));
			$write_status = fwrite($new_image, $image_data);
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
	 * @param string $file_path System path to image source
	 * @param string $user_id User ownership
	 * @param string $type String classification
	 * @return boolean
	 */
	public function saveImage($file_path, $user_id, $type = 'Image', $options = array())
	{
		$image = WideImage::load($file_path);

		if($image === false) {
			$this->log('Could not open file upload.');
			return false;
		}

		$image_name = CakeText::uuid() . '.jpg';
		$folder = $this->folderPath . $user_id . DS;
		if(!file_exists($folder)) {
			$dir = new Folder($folder, true, 0755);
		}
		$new_path = $folder . $image_name;

		if(!empty($options['crop'])) {
			$cropped = $image->crop($options['crop']['x1'], $options['crop']['y1'], $options['crop']['w'], $options['crop']['h']);

		// resize standard images
		} else {
			$sizing = getimagesize($file_path);

			// don't overstretch small images
			if($sizing[0] < $this->minDimensions && $sizing[1] < $this->minDimensions) {
				$cropped = $image->resize($this->minDimensions, $this->minDimensions);
			} else {
				$cropped = $image->resize($this->maxDimensions, $this->maxDimensions);
			}
		}

		// detect if the image needs rotation based on available EXIF data
		$exif = @exif_read_data($file_path);
		if(isset($exif['Orientation']) && is_numeric($exif['Orientation'])) {

			// 180 deg flip (upside-down)
			if($exif['Orientation'] === 3) {
				$cropped = $cropped->rotate(180);

			// counter 90 deg
			} elseif($exif['Orientation'] === 6) {
				$cropped = $cropped->rotate(90);

			// 90 deg
			} elseif($exif['Orientation'] === 8) {
				$cropped = $cropped->rotate(-90);
			}
		}

		$cropped->saveToFile($new_path, $this->jpegQuality);
		$cropped->releaseHandle();
		$image->releaseHandle();

		$data = array(
			'type' => $type,
			'filename' => $image_name,
			'ext' => 'jpg',
			'checksum' => -1,
			'user_id' => $user_id
		);

		if(!empty($options['album_id'])) {
			$data['album_id'] = $options['album_id'];
		}

		$this->create();
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
	 * Resizes an image on disk, generating thumbnail versions of the original
	 *
	 * @param string $imagePath Path to image to process
	 * @return boolean
	 */
	public function saveThumbs($imagePath)
	{
		$image = WideImage::load($imagePath);

		if($image === false) {
			$this->log('Could not open original image for thumb generation');
			return false;
		}

		$base_path = dirname($imagePath) . DS;
		$filename = substr($imagePath, strlen($base_path));

		foreach($this->imageThumbs as $width) {
			$cropped = $image->resize($width, $width);

			$folder = $base_path . DS . $width . DS;
			if(!file_exists($folder)) {
				$dir = new Folder($folder, true, 0755);
				unset($dir);
			}

			$cropped->saveToFile($folder . $filename, $this->jpegQuality);
			$cropped->releaseHandle();
			unset($cropped);
		}

		$image->releaseHandle();
		return true;
	}

	/**
	 * Prepare a single asset to post to Facebook
	 *
	 * @param string $asset_id Asset primary key to post
	 * @return array
	 */
	public function castToFacebook($asset_id)
	{
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
	 * @param boolean $cascade
	 * @return boolean
	 */
	public function beforeDelete($cascade = true)
	{
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

			// remove any contest entries, if applicable
			ClassRegistry::init('AssetsContest')->deleteAll(array('asset_id' => $this->id));

			return true;
		}
		return false;
	}

	/**
	 * Obtains the list of Asset types
	 *
	 * @return array Set of existing Asset types
	 */
	public function getTypes()
	{
		$types = $this->find('list', array(
			'fields' => 'type',
			'group' => "{$this->alias}.type",
			'order' => "{$this->alias}.type ASC"
		));

		return array_combine($types, $types);
	}

	/**
	 * Converts the available Activity model and relationship data to reduce
	 * it to a human-friendly sentence.
	 *
	 * @param  {ActivityModel} $activity Activity to convert
	 */
	public function humanizeActivity(&$activity)
	{
		switch($activity['Asset']['type']) {
			case 'Contest':
				$activity['Activity']['phrase'] = ":user saved a Caption Battle entry.";
				$activity['Activity']['icon'] = 'inbox-plus';
				break;
			case 'Meme' :
				$activity['Activity']['phrase'] = ":user saved a Meme.";
				$activity['Activity']['icon'] = 'slide-pencil';
				$activity['Activity']['icon'] = 'image-pencil';
				break;
			case 'URLgrab':
				if(!empty($activity['Asset']['album_id'])) {
					$activity['Activity']['phrase'] = ":user saved a web image to an album.";
					$activity['Activity']['icon'] = 'photo-album-plus';
				} else {
					$activity['Activity']['phrase'] = ":user saved a web image from a URL.";
					$activity['Activity']['icon'] = 'network-cloud';
				}
				break;
			case 'Upload':
				if(!empty($activity['Asset']['album_id'])) {
					$activity['Activity']['phrase'] = ":user uploaded a new album photo.";
					$activity['Activity']['icon'] = 'photo-album-plus';
				} else {
					$activity['Activity']['phrase'] = ":user uploaded a new image.";
					$activity['Activity']['icon'] = 'drive-upload';
				}
				break;
			case 'Crop':
				$activity['Activity']['phrase'] = ":user cropped an image.";
				$activity['Activity']['icon'] = 'ruler-crop';
				break;
			default:
				$activity['Activity']['phrase'] = ":user saved a new image.";
				$activity['Activity']['icon'] = 'image-plus';
		}
		$activity['Activity']['link'] = array('controller' => 'assets', 'action' => 'view', $activity['Asset']['id'], '#' => 'image');
		$activity['Activity']['preview'] = "{$this->folderPathRelative}{$activity['Asset']['user_id']}/200/{$activity['Asset']['filename']}";
		$activity['Activity']['preview-small'] = "{$this->folderPathRelative}{$activity['Asset']['user_id']}/75/{$activity['Asset']['filename']}";
	}

	/**
	 * Determines if an image creation should be announced to other users
	 *
	 * @param array $data Asset model data
	 * @return boolean false to suppress the announcement
	 */
	public function notificationInclusion($data)
	{
		if(!empty($data[$this->alias]['album_id'])) {
			$album = $this->Album->findById($data[$this->alias]['album_id']);

			if($album) {
				$album_creation = strtotime($album['Album']['created']);

				// exclude images uploaded within a small window following the album's creation
				return time() > $album_creation + (1 * DAY);
			}
		}
		return true;
	}

	/**
	 * Obtains the conditions to find Assets which are ready for the Meme
	 * Generator. This is typically images without text on them.
	 *
	 * @return array Find conditions
	 */
	public function getCleanImageConditions()
	{
		return array(
			'Asset.type' => array(
				'Crop', 'Upload', 'URLgrab'
			)
		);
	}

	/**
	 * Count-wrapper for getCleanImageConditions
	 */
	public function getCleanImageCount()
	{
		return $this->find('count', array(
			'conditions' => $this->getCleanImageConditions()
		));
	}

	/**
	 * Get the list of Users who have at least one image contribution
	 * @return array
	 */
	public function getContributingUsers()
	{
		return $this->User->find('all', array(
			'joins' => array(
				array(
					'table' => $this->table,
					'alias' => $this->alias,
					'type' => 'inner',
					'conditions' => array(
						"{$this->alias}.user_id = {$this->User->alias}.{$this->User->primaryKey}"
					)
				)
			),
			'group' => 'User.id',
			'order' => 'COUNT(*) DESC'
		));
	}
}