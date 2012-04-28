<?php
App::uses('AppModel', 'Model');
/**
 * Message Model
 *
 * @property User $User
 */
class Message extends AppModel {

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	public function countNewMessages($user_id, $since = false) {
		if($since === false) {
			$since = $this->User->field('last_ack',array('id'=>$user_id));
			
			if($since === false) {
				$since = date(MYSQL_DATE_FORMAT,strtotime('now - 1 day'));
			}
		}	
		return $this->find('count',array(
			'conditions'=>array(
				'user_id <>' => $user_id,
				'created >=' => $since
			)
		));
	}
	
	public function getNewMessages($since, $exclude_from = false) {
		$query = array(
			'contain'=>'User',
			'conditions'=>array(
				'Message.created >='=>$since
			),
			'order'=>'Message.created DESC',
			'limit'=>50
		);
		if($exclude_from !== false) {
			$query['conditions']['NOT']['Message.user_id'] = $exclude_from;
		}
		$results = $this->find('all',$query);
		
		foreach ($results as &$result) {
			$result['Message']['timestamp'] = strtotime($result['Message']['created']);
		}
		return $results;
	}
}
