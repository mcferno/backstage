<?php

App::uses('Helper', 'AppHelper');

class SiteHelper extends AppHelper
{
	public $helpers = array('Html', 'Text');

	/**
	 * Inspects the Post Model data to determine the most appropriate profile
	 * image.
	 *
	 * @param array $data Post Model data
	 * @return string Image HTML
	 */
	public function profileImage($data)
	{
		if(!empty($data['Post']['model'])) {
			if($data['Post']['model'] == 'Tumblr') {
				if(stripos($data['Post']['source'], 'guest') !== false) {
					return $this->Html->image('profile/guest-profile96.jpg', array('alt' => 'Guest', 'title' => 'Guest'));
				} else {
					return $this->Html->image('profile/kqm-profile96.jpg', array('alt' => 'The Man Himself', 'title' => 'The Man Himself'));
				}
			} elseif($data['Post']['model'] == 'Twitter') {
				if(stripos($data['Post']['permalink'], 'fakeclouds')) {
					$image = $this->Html->image($this->_View->viewVars['accounts']['fakeclouds'], array('width' => 96, 'height' => 96, 'alt' => '@fakeclouds', 'title' => '@fakeclouds'));
				} else {
					$image = $this->Html->image($this->_View->viewVars['accounts']['SexistSquash'], array('width' => 96, 'height' => 96, 'alt' => '@SexistSquash', 'title' => '@SexistSquash'));
				}

				if(!empty($data['Post']['permalink'])) {
					return $this->Html->link($image, $data['Post']['permalink'], array('escape' => false));
				} else {
					return $image;
				}
			}
		}
	}

	/**
	 * Produce an SEO-friendly slug from the Post body
	 *
	 * @param array $post Post model data
	 * @return string Sluggified, truncated slug
	 */
	public function postSlug($post)
	{
		// strip out fancy html characters
		$text = strtr($post['Post']['body'], array(
			'&#8217;' => '\'',
			'&#160;' => ' '
		));
		return strtolower(
			Inflector::slug(
				$this->Text->truncate(
					$text,
					45,
					array('exact' => false, 'ending' => '')
				),
				'-'
			)
		);
	}

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