<?php
/**
 * Simple Tumblr Model/Datasource. Assists in content scraping and local mirroring
 */
class Tumblr extends AppModel {

	public $useTable = 'tumblr';

	public $actsAs = array('Postable.Postable'=>array(
		'mapping'=>array(
			'body'=>'text',
			'date'=>'timestamp',
			'permalink'=>'post_url'
		),
		'inclusionCallback'=>'postableInclusion'
	));

	/**
	 * Pulls the latest post from the Tumblr source, saving or updating any
	 * existing records applicable.
	 */
	public function refresh() {

		/**
		 * Build up the request for new posts
		 * http://www.tumblr.com/docs/en/api/v2#posts
		 */
		$base_url = 'http://api.tumblr.com/v2/blog/kennyquotemachine.tumblr.com';
		$params = array(
			'api_key'=>'sRZhuUQgFJ5vArAbDrf9nglWzsPnu5vXKwKLDspeuGXvABEqw9',
			'format'=>'text',

			// offset by the number of posts we have on hand
			//'offset' => $this->find('count')
		);

		$records = $this->_readJson("{$base_url}/posts",$params);

		if(empty($records['response']['posts'])) {
			return;
		}

		foreach($records['response']['posts'] as $post) {

			// check for existing records to update.
			$existing = $this->find('first',array(
				'conditions'=>array(
					'tumblr_id'=>$post['id']
				)
			));

			$this->create();

			// base set of fields we are sure will be present
			$post_data = array(
				'blog_name'=>$post['blog_name'],
				'tumblr_id'=>$post['id'],
				'post_url'=>$post['post_url'],
				'type'=>$post['type'],
				'timestamp'=>$post['timestamp'],
			);

			// set the primary key to trigger an update if this record exists
			if(!empty($existing[$this->alias]['id'])) {
				$post_data['id'] = $existing[$this->alias]['id'];
			}

			switch($post['type']) {
				case 'quote':
					$post_data['text'] = $post['text'];
					$post_data['source'] = $post['source'];
					break;
				case 'text':
					$post_data['text'] = $post['body'];
					$post_data['source'] = $post['title'];
					break;
				case 'photo':
					$post_data['text'] = json_encode($post['photos']);
					$post_data['source'] = $post['caption'];
					break;
				default:
					continue; // don't save this record
			}

			$this->save($post_data);
		}
	}

	public function postableInclusion($data) {
		return ($data[$this->alias]['type'] != 'photo');
	}
}