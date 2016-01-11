<?php

/**
 * Simple Tumblr Model/Datasource. Assists in content scraping and local mirroring
 */
class Tumblr extends AppModel
{
	public $useTable = 'tumblr';

	public $actsAs = array('Postable.Postable' => array(
		'mapping' => array(
			'body' => 'text',
			'date' => 'timestamp',
			'permalink' => 'post_url'
		),
		'inclusionCallback' => 'postableInclusion'
	));

	public $uses = array('Account');

	protected $api_key = null;

	public function __construct($id = false, $table = null, $ds = null)
	{
		parent::__construct($id, $table, $ds);
		try {
			Configure::load('tumblr');
			$this->api_key = Configure::read('Tumblr_App.api_key');
		} catch(ConfigureException $e) {
			$this->log('Could not load the Tumbler app settings');
			return;
		}
	}

	/**
	 * Pulls the latest post from the Tumblr source, saving or updating any
	 * existing records applicable.
	 *
	 * @return boolean
	 */
	public function refresh()
	{
		if(is_null($this->api_key)) {
			$this->log('Cant refresh accounts without api keys');
			return false;
		}

		$accounts = ClassRegistry::init('Account')->find('all', array(
			'fields' => array('id', 'handle'),
			'conditions' => array(
				'type' => 'Tumblr',
				'active' => true
			)
		));

		foreach($accounts as $account) {
			$this->_pullRecentPosts($account['Account']['handle']);
		}

		return true;
	}

	protected function _pullRecentPosts($base)
	{

		/**
		 * Build up the request for new posts
		 * http://www.tumblr.com/docs/en/api/v2#posts
		 */
		$base_url = "http://api.tumblr.com/v2/blog/{$base}";
		$params = array(
			'api_key' => $this->api_key,
			'format' => 'text'
		);

		$records = $this->_readJson("{$base_url}/posts", $params);

		if(empty($records['response']['posts'])) {
			return;
		}

		foreach($records['response']['posts'] as $post) {

			// check for existing records to update.
			$existing = $this->find('first', array(
				'conditions' => array(
					'tumblr_id' => $post['id']
				)
			));

			$this->create();

			// base set of fields we are sure will be present
			$post_data = array(
				'blog_name' => $post['blog_name'],
				'tumblr_id' => $post['id'],
				'post_url' => $post['post_url'],
				'type' => $post['type'],
				'timestamp' => $post['timestamp'],
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

	public function postableInclusion($data)
	{
		return ($data[$this->alias]['type'] != 'photo');
	}
}