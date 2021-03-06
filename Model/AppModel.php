<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 * @method boolean hasAny
 * @method array findById
 */
class AppModel extends Model
{
	public $recursive = -1;
	public $actsAs = array('Containable');

	/**
	 * Pulls a JSON feed via URL and returns the decoded format. Simple wrapper
	 * for GET-style API pulls.
	 *
	 * @param string $url Base URL to retrieve
	 * @param array $args GET-params
	 * @return array JSON-decoded results
	 */
	protected function _readJson($url, $args)
	{
		$source = $url . '?' . http_build_query($args);

		$json = file_get_contents($source);
		if($json === false) {
			return array();
		}
		return json_decode($json, true);
	}
}
