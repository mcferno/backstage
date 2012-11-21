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

		// creator of the message
		'User',

		// object being written about (when applicable)
		'Asset' => array('foreignKey' => 'foreign_id'),
		'Contest' => array('foreignKey' => 'foreign_id'),
		'Link' => array('foreignKey' => 'foreign_id')
	);

	public $actsAs = array('Postable.Postable' => array(
		'storageModel' => 'Activity',
		'inclusionCallback' => 'activityFeedInclusion'
	));

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
	
	public function countNewMessages($scope, $user_id, $since = false) {
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
	
	public function getNewMessages($scope, $scopeId = false, $since = false, $exclude_from = false) {
		$query = array(
			'contain'=> array(
				'User' => array(
					'fields' => array('username')
				)
			),
			'fields' => array(
				'id', 'created', 'text'
			),
			'conditions'=>array(
				'Message.model' => $scope
			),
			'order'=>'Message.created DESC'
		);

		if($scopeId !== false) {
			$query['conditions']['Message.foreign_id'] = $scopeId;
		}
		if($since !== false) {
			$query['conditions']['Message.created >='] = $since;
			$query['limit'] = 50;
		}
		if($exclude_from !== false) {
			$query['conditions']['NOT']['Message.user_id'] = $exclude_from;
		}

		$results = $this->find('all',$query);
		
		foreach ($results as &$result) {
			$result['Message']['timestamp'] = strtotime($result['Message']['created']);
		}
		return $results;
	}

	public function activityFeedInclusion($data) {
		return ($data['Message']['model'] !== 'Chat');
	}

	/**
	 * Converts the available Activity model and relationship data to reduce
	 * it to a human-friendly sentence.
	 * 
	 * @param {ActivityModel} $activity Activity to convert
	 */
	public function humanizeActivity(&$activity) {
		$activity['Activity']['phrase'] = ":user commented";
		$activity['Activity']['icon'] = 'balloon-ellipsis';

		switch($activity['Message']['model']) {
			case 'Contest':
				$activity['Activity']['link'] = array('controller' => 'contests', 'action' => 'view', $activity['Message']['foreign_id']);
				$activity['Activity']['phrase'] .= ' on a Caption Battle.';

				if(!empty($activity['Message']['Contest']['Asset']['id'])) {
					$activity['Activity']['preview'] = "{$this->Asset->folderPathRelative}{$activity['Message']['Contest']['Asset']['user_id']}/200/{$activity['Message']['Contest']['Asset']['filename']}";
					$activity['Activity']['preview-small'] = "{$this->Asset->folderPathRelative}{$activity['Message']['Contest']['Asset']['user_id']}/75/{$activity['Message']['Contest']['Asset']['filename']}";
				}
				break;
			case 'Asset':
				$activity['Activity']['link'] = array('controller' => 'assets', 'action' => 'view', $activity['Message']['foreign_id']);
				$activity['Activity']['phrase'] .= ' on an image.';

				if(!empty($activity['Message']['Asset']['id'])) {
					$activity['Activity']['preview'] = "{$this->Asset->folderPathRelative}{$activity['Message']['Asset']['user_id']}/200/{$activity['Message']['Asset']['filename']}";
					$activity['Activity']['preview-small'] = "{$this->Asset->folderPathRelative}{$activity['Message']['Asset']['user_id']}/75/{$activity['Message']['Asset']['filename']}";
				}
				break;
			case 'Link':
				$activity['Activity']['link'] = array('controller' => 'links', 'action' => 'view', $activity['Message']['foreign_id']);
				
				if(!empty($activity['Message']['Link']['title'])) {
					$activity['Activity']['phrase'] .= " on the {$activity['Message']['Link']['title']} link.";
				} else {
					$activity['Activity']['phrase'] .= ' on a link.';
				}
				break;
		}
	}

	/**
	 * Obtains a message count for all types of content in a lookup array, similar
	 * to the output of ->find('list')
	 *
	 * @return  {Array} Array indexed by the foreign_id, with its message count as the value
	 */
	public function getTally($conditions = array()) {

		$tally = $this->find('all', array(
			'fields' => array('COUNT(*) as count', 'foreign_id'),
			'group' => 'foreign_id',
			'conditions' => $conditions
		));

		return Hash::combine($tally, '{n}.Message.foreign_id', '{n}.0.count');
	}
}
