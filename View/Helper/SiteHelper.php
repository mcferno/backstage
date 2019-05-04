<?php

App::uses('Helper', 'AppHelper');

class SiteHelper extends AppHelper
{
	public $helpers = array('Html', 'Text');

	/**
	 * Collects various visitor details for feature detection and UX
	 */
	public function userDetails()
	{
		return array(
			'isMobile' => $this->request->is('mobile')
		);
	}

	public function jsBasePath($str)
	{
		if(substr($str, -1) !== '/') {
			$str .= '/';
		}

		return $str;
	}
}