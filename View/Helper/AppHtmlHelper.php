<?php
App::uses('Helper', 'View');
App::uses('HtmlHelper', 'View/Helper');

class AppHtmlHelper extends HtmlHelper {

	public function image($path, $options = array()) {

		// add in file mtime to path
		if(isset($options['cachebust']) && $options['cachebust'] 
			&& stripos($path, 'http') === false
			&& file_exists(IMAGES . $path)) {
			
			$path .= '?' . filemtime(IMAGES . $path);
			unset($options['cachebust']);
		}

		return parent::image($path, $options);
	}

}