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
	public $rolloverTime = MESSAGES_DEFAULT_ROLLOVER;

	// rollover time, based on the current timestamp (computed at runtime)
	public $minimumSince = -1;

	public $belongsTo = array(

		// creator of the message
		'User',

		// object being written about (when applicable)
		'Asset' => array('foreignKey' => 'foreign_id'),
		'Contest' => array('foreignKey' => 'foreign_id'),
		'Link' => array('foreignKey' => 'foreign_id'),
		'Video' => array('foreignKey' => 'foreign_id')
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
			$query['limit'] = MESSAGES_DEFAULT_BUFFER;
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
	 * Messages are attached to existing content (Model), so we leverage that 
	 * Model's humanization first.
	 * 
	 * @param {ActivityModel} $activity Activity to convert
	 */
	public function humanizeActivity(&$activity) {

		// remap the inner-model to leverage its humanization
		if(!empty($activity['Message'][$activity['Message']['model']]['id'])) {
			$activity[$activity['Message']['model']] = $activity['Message'][$activity['Message']['model']];

			if(isset($this->{$activity['Message']['model']})) {
				$this->{$activity['Message']['model']}->humanizeActivity($activity);
			}
		}

		$activity['Activity']['phrase'] = ":user commented";
		$activity['Activity']['icon'] = 'balloon-ellipsis';

		// specialize the information message based on the Model it applied to
		switch($activity['Message']['model']) {
			case 'Contest':
				$activity['Activity']['phrase'] .= ' on a Caption Battle.';
				break;
			case 'Asset':
				$activity['Activity']['phrase'] .= ' on an image.';
				break;
			case 'Link':
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
