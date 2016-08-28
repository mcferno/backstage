<?php

/**
 * Dynamically generated HTML markup, suited for higher performance than View Partials.
 */
class SnippetHelper	extends AppHelper
{
	public $helpers = array('Html');

	protected $snippets = array(
		'asset' => '<a href="%s" data-id="%s"><img src="%s" alt="" title="%s"></a>'
	);

	/**
	 * Generate a thumbnail and anchor link to view the full size image
	 *
	 * @param $asset Asset model
	 * @return string
	 */
	public function assetThumbnail($asset)
	{
		$url = $this->url(array('action' => 'view', $asset['Asset']['id'], '#' => 'image'));
		$image = $this->webroot(IMAGES_URL . $asset['Asset']['image-thumb']);
		$title = !empty($asset['User']['id'])
			? "@{$asset['User']['username']} on "
			: '';
		$title .= date('M jS, Y', strtotime($asset['Asset']['created']));

		return sprintf($this->snippets['asset'],
			$url, $asset['Asset']['id'], $image, $title
		);
	}
}