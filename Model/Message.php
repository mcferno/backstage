<?php
App::uses('AppModel', 'Model');
App::uses('Sanitize', 'Utility');
/**
 * Message Model
 *
 * @property User $User
 */
class Message extends AppModel {

	// offset from "now" in seconds, to determine which messages are new
	public $rolloverTime = DAY;

	// rollover time, based on the current timestamp (computed at runtime)
	public $minimumSince = -1;

	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		// compute the utility value timestamp
		$this->minimumSince = time() - $this->rolloverTime;
	}

	public function beforeSave($options = array()) {

		// sanitize for possible xss
		if(empty($this->data[$this->alias]['id']) && !empty($this->data[$this->alias]['text'])) {
			$this->data[$this->alias]['text'] = Sanitize::html($this->data[$this->alias]['text']);
		}

		return true;
	}
	
	public function countNewMessages($user_id, $since = false, $scope = 'Chat', $scopeId = false) {
		if($since === false) {
			$since = $this->User->field('last_ack',array('id'=>$user_id));
			
			// since values was not found, or exceeds the max elapsed time.
			if($since === false || strtotime($since) < $this->minimumSince) {
				$since = date(MYSQL_DATE_FORMAT, $this->minimumSince);
			}
		}	
		return $this->find('count',array(
			'conditions'=>array(
				'user_id <>' => $user_id,
				'created >=' => $since,
				'model' => $scope
			)
		));
	}
	
	public function getNewMessages($since, $exclude_from = false, $scope = 'Chat', $scopeId = false) {
		$query = array(
			'contain'=>'User',
			'conditions'=>array(
				'Message.created >='=>$since,
				'Message.model' => $scope
			),
			'order'=>'Message.created DESC',
			'limit'=>50
		);
		if($exclude_from !== false) {
			$query['conditions']['NOT']['Message.user_id'] = $exclude_from;
		}
		if($scopeId !== false) {
			$query['conditions']['Message.foreign_id'] = $scopeId;
		}
		$results = $this->find('all',$query);
		
		foreach ($results as &$result) {
			$result['Message']['timestamp'] = strtotime($result['Message']['created']);
		}
		return $results;
	}
}
