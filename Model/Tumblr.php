<?php
/**
 * Simple Tumblr Model/Datasource.
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
	
	public function lazyCron() {
		
		if(Cache::read('cron','short') === false) {
			Cache::write('cron', 'x', 'short');
			$this->refresh();
		}
	}
	
	/**
	 * Pulls the latest post from the Tumblr source, saving or updating any 
	 * records applicable.
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
	
	/**
	 * Pulls a JSON feed via URL and returns the decoded format. Simple wrapper
	 * for GET-style API pulls.
	 *
	 * @param {String} $url Base URL to retrieve
	 * @param {Array} $args GET-params
	 * @return {Array} JSON-decoded results
	 */
	protected function _readJson($url, $args) {
		$source = $url.'?'.http_build_query($args);
		
		$json = file_get_contents($source);
		if($json === false) {
			return array();
		}
		return json_decode($json,true);
	}
	
	public function postableInclusion($data) {
		return ($data[$this->alias]['type'] != 'photo');
	}
}