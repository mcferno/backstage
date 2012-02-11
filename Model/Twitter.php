<?php

class Twitter extends AppModel {
	
	public $useTable = 'twitter';
	
	public $actsAs = array('Postable.Postable'=>array(
		'mapping'=>array(
			'body'=>'text',
			'date'=>'created_at',
			'permalink'=>'source',
			'source'=>false
		)
	));
	
	public function lazyCron() {
		
		if(Cache::read($this->alias.'_cron','short') === false) {
			Cache::write($this->alias.'_cron', 'x', 'short');
			$this->refresh();
		}
	}
	
	public function refresh() {
		//https://dev.twitter.com/docs/api/1/get/statuses/user_timeline
		//https://api.twitter.com/1/statuses/user_timeline.json?include_entities=true&include_rts=true&screen_name=twitterapi&count=2
		
		$accounts = ClassRegistry::init('Account')->find('all',array('fields'=>array('id','handle')));
		
		foreach($accounts as $account) {

			ClassRegistry::init('Account')->follow($account['Account']['handle']);
			
			$base_url = 'https://api.twitter.com/1/statuses/user_timeline.json';
			$params = array(
				'screen_name'=>$account['Account']['handle'],
				'count'=>'100'
			);
			
			$records = $this->_readJson($base_url, $params);
			
			foreach($records as $record) {
				$this->create();
				
				// base set of fields we are sure will be present
				$post_data = array(
					'id'=>$record['id_str'],
					'created_at'=>strtotime($record['created_at']),
					'text'=>$record['text'],
					'source'=>'https://twitter.com/#!/'.$params['screen_name'].'/status/'.$record['id_str'],
					'truncated'=>$record['truncated'],
					'in_reply_to_status_id'=>$record['in_reply_to_status_id_str'],
					'in_reply_to_user_id'=>$record['in_reply_to_user_id_str'],
					'in_reply_to_screen_name'=>$record['in_reply_to_screen_name'],
					'favorited'=>$record['favorited'],
					'user_id'=>$record['user']['id_str'],
					'data'=>json_encode($record)
				);
				
				$this->save($post_data);
			}
		}
	}
}