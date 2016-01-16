<?php

/**
 * Simple Twitter Model/Datasource. Assists in content scraping and local mirroring
 */
class Twitter extends AppModel
{
	public $useTable = 'twitter';

	public $actsAs = array('Postable.Postable' => array(
		'mapping' => array(
			'body' => 'text',
			'date' => 'created_at',
			'permalink' => 'source',
			'source' => false
		),
		'inclusionCallback' => 'postableInclusion'
	));

	/**
	 * Pull the latest tweets from all Accounts currently tracked by the system
	 */
	public function refresh()
	{
		$accounts = ClassRegistry::init('Account')->find('all', array(
			'fields' => array('id', 'handle', 'user_id'),
			'conditions' => array(
				'type' => 'Twitter',
				'active' => true
			)
		));
		$cb = $this->getAPIObject();

		foreach($accounts as $account) {

			// Refresh the user's meta data (profile image)
			ClassRegistry::init('Account')->follow($account['Account']['handle']);

			// API tweet search parameters
			$params = array(
				'screen_name' => $account['Account']['handle'],
				'count' => '100'
			);

			// attempt to find the last available tweet.
			$last_tweet = $this->getLatestTweet($account['Account']['user_id']);
			if(!empty($last_tweet['Twitter']['id'])) {
				$params['since_id'] = $last_tweet['Twitter']['id'];
			}

			$records = $cb->statuses_userTimeline($params, true);

			foreach($records as $record) {
				if(!empty($record['id_str'])) {
					$this->create();

					// base set of fields we are sure will be present
					$post_data = array(
						'id' => $record['id_str'],
						'created_at' => strtotime($record['created_at']),
						'text' => $record['text'],
						'source' => 'https://twitter.com/' . $params['screen_name'] . '/status/' . $record['id_str'],
						'truncated' => $record['truncated'],
						'in_reply_to_status_id' => $record['in_reply_to_status_id_str'],
						'in_reply_to_user_id' => $record['in_reply_to_user_id_str'],
						'in_reply_to_screen_name' => $record['in_reply_to_screen_name'],
						'favorited' => $record['favorited'],
						'user_id' => $record['user']['id_str'],
						'data' => json_encode($record)
					);

					$this->save($post_data);
				}
			}
		}
	}

	/**
	 * Loads the CodeBird library and initializes the basic auth settings.
	 * @return \CodeBird
	 */
	public function getAPIObject()
	{
		try {
			Configure::load('twitter');
		} catch(ConfigureException $e) {
			$this->log('Could not load the Twitter app settings');
			return false;
		}

		require_once(APP . 'Vendor' . DS . 'codebird-php' . DS . 'src' . DS . 'codebird.php');

		$cb = Codebird::getInstance();
		$cb->setConsumerKey(
			Configure::read('Twitter_App.consumer_key'),
			Configure::read('Twitter_App.consumer_secret')
		);
		$cb->setReturnFormat(CODEBIRD_RETURNFORMAT_ARRAY);
		$cb->setBearerToken(Configure::read('Twitter_App.bearer_token'));

		return $cb;
	}

	/**
	 * Obtains the latest tweet from the local DB
	 *
	 * @param string|int $user_id Twitter account number
	 * @return array Latest tweet record
	 */
	public function getLatestTweet($user_id)
	{
		return $this->find('first', array(
			'conditions' => array(
				'user_id' => $user_id
			),
			'order' => 'created_at DESC'
		));
	}

	/**
	 * Postable Behavior callback to determine which Twitter messages are included
	 * in the "Post" index.
	 *
	 * @param array $data Model save data
	 * @return boolean Whether to include this post in the index.
	 */
	public function postableInclusion($data)
	{
		return empty($data[$this->alias]['in_reply_to_user_id']);
	}
}