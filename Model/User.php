<?php
App::uses('AppModel', 'Model');
App::uses('AuthComponent', 'Controller/Component');

class User extends AppModel {

	public $displayField = 'username';

	public $validate = array(
		'username' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Username is required',
				'required' => true
			),
			'formatting' => array(
				'rule' => '/^[a-zA-Z0-9-_]{1,20}$/',
				'message' => 'Uppercase, lowercase, numbers, underscores, and dashes only.',
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Sorry, that username is already taken.',
			),
		),
		'password' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Password is required'
			),
		),
		'role' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'User role must be specified'
			),
		),
	);
	
	public function beforeSave() {
	    if (!empty($this->data[$this->alias]['password'])) {
	        $this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
	    }
	    return true;
	}
	
	/**
	 * Tracks the last time the user authenticated with the server.
	 *
	 * @param {UUID} $user_id User to update
	 * @param {Integer} $timestamp Unix timestamp of the visit
	 * @return {Boolean} Update status
	 */
	public function setLastLogin($user_id, $timestamp) {
		return $this->updateAll(
			array("{$this->alias}.last_login" => '\''.date(MYSQL_DATE_FORMAT,$timestamp).'\''),
			array("{$this->alias}.{$this->primaryKey}" => $user_id)
		);
	}
	
	/**
	 * Tracks the last time the user made any activity with the server.
	 *
	 * @param {UUID} $user_id User to update
	 * @param {Integer} $timestamp Unix timestamp of the visit
	 * @return {Boolean} Update status
	 */
	public function setLastSeen($user_id, $timestamp) {
		return $this->updateAll(
			array("{$this->alias}.last_seen" => '\''.date(MYSQL_DATE_FORMAT,$timestamp).'\''),
			array("{$this->alias}.{$this->primaryKey}" => $user_id)
		);
	}
	
	/**
	 * Tracks the last time the user made any activity with the server.
	 *
	 * @param {UUID} $user_id User to update
	 * @param {Integer} $timestamp Unix timestamp of the visit
	 * @return {Boolean} Update status
	 */
	public function setLastAck($user_id, $timestamp) {
		$datetime = date(MYSQL_DATE_FORMAT,$timestamp);
		return $this->updateAll(
			array("{$this->alias}.last_ack" => '\''.$datetime.'\''),
			array(
				"{$this->alias}.{$this->primaryKey}" => $user_id,
				"{$this->alias}.last_ack <" => $datetime
			)
		);
	}
	
	public function getOnlineUsers() {
		$users = Cache::read('onlineUsers','online_status');
		
		// cache-miss
		if($users === false) {
			$users = $this->find('all',array(
				'fields'=>array('username','last_seen'),
				'conditions'=>array(
					'last_seen >='=>date(MYSQL_DATE_FORMAT,strtotime('now - 10 minutes'))
				)
			));
			Cache::write('onlineUsers', $users, 'online_status');
		}
		
		return $users;
	}
}
