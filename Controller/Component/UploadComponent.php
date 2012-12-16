<?php
App::uses('Component', 'Controller');
App::uses('Validation', 'Utility');

/**
 * Handles the validation and manipulation of uploaded content originating from
 * POSTed data, and URL retrieval.
 */
class UploadComponent extends Component {

	public $mimeTypes = false;
	public $fileExtensions = false;
	public $Controller = false;

	public function initialize(&$controller) {
		parent::initialize($controller);
		$this->Controller = $controller;
	}

	/**
	 * Captures a URL, saving the contents to a file
	 *
	 * @param {String} $url HTTP/s url to capture
	 * @param {String} $destination File path to write into, otherwise a tmp file is used
	 * @return {String | false} System path the to downloaded asset
	 */
	public function saveURLtoFile($url, $destination = false) {

		$filePath = ($destination) ? $destination : tempnam(TMP , 'urlsave_');
		$fileHandle = fopen($filePath, "w+");

		$result = false;

		if($fileHandle !== false) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
			curl_setopt($ch, CURLOPT_TIMEOUT, 15);
			curl_setopt($ch, CURLOPT_FILE, $fileHandle);
			
			// successful save from url
			if(curl_exec($ch) !== false) {

				fflush($fileHandle);
				fclose($fileHandle);

				// restrict the file mime-types if provided
				if($this->mimeTypes === false || in_array($this->getMimeType($filePath), $this->mimeTypes)) {
					$result = $filePath;
				}

			// failure to curl, scrap the temp file
			} else {
				fclose($fileHandle);
				if(file_exists($filePath)) {
					unlink($filePath);
				}
			}
			curl_close($ch);
		}

		return $result;
	}

	/**
	 * Determines whether a file upload is valid
	 * 
	 * @param {Array} $payload Nested $_FILES content for inspection
	 * @return {true|String} True if the payload is good, error message otherwise
	 */
	public function isValidUpload($payload) {

		// file upload error
		if($payload['error'] !== 0 || !file_exists($payload['tmp_name'])) {
			return 'Image upload has failed, please try again.';
		}

		// mime-type error
		if(!in_array($payload['type'], $this->mimeTypes)) {
			return 'Sorry, ' . implode(', ', $this->mimeTypes) . ' uploads only.';
		}

		return true;
	}

	/**
	 * Determines whether the provided file URL is valid, and matches allowable
	 * extensions
	 * 
	 * @param {String} $payload URL to inspect
	 * @return {true|String} True if the URL is good, error message otherwise
	 */
	public function isValidURL($payload) {

		if(!Validation::url($payload)) {
			return 'Invalid URL provided.';
		}

		$url_parts = parse_url($payload);
		$extension_regex = implode('|', $this->fileExtensions);

		// restrict URL capture to known extensions
		if(!preg_match('/\.(' . $extension_regex . ')$/i', $url_parts['path'])) {
			return 'Only ' . implode(', ', $this->fileExtensions) . ' are accepted via URL, please try a different URL.';
		}

		return true;
	}

	/**
	 * Obtains the mime-type of the provided file path
	 * 
	 * @param {String} Server path to the desired file
	 * @return {String|false} Mime-type detected, or false on error
	 */
	public function getMimeType($filePath) {
		if(!file_exists($filePath)) {
			return false;
		}

		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		return strtolower(finfo_file($finfo, $filePath));
	}

	/**
	 * Obtains the filename extension of a system path or URL
	 * 
	 * @param {String} $path String to inspect
	 * @return {String} File extension
	 */
	public function getExtension($path) {
		$matches = array();

		if(preg_match('/\.([a-z0-9]{2,4})$/i', $path, $matches)) {
			return strtolower($matches[1]);
		}

		return false;
	}
}