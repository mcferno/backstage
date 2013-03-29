<?php
App::uses('AppModel', 'Model');
App::uses('AuthComponent', 'Controller/Component');

class User extends AppModel {

	public $displayField = 'username';
	public $hasMany = array('Asset', 'Contest');
	public $attachTimeDeltas = false;
	
	protected $facebookObj = false;

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
	
	public function beforeSave($options = array()) {
	    if (!empty($this->data[$this->alias]['password'])) {
	        $this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
	    }
	    return true;
	}

	public function afterFind($results, $primary = false) {
		if($this->attachTimeDeltas) {
			$now = Configure::read('App.start');
			foreach($results as &$result) {
				if(isset($result['User']['last_ack'])) {
					$result['User']['last_ack_delta'] = $now - strtotime($result['User']['last_ack']);
				}
			}
		}

		return $results;
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

	/**
	 * Tracks the last time the user viewed system notifications.
	 *
	 * @param {UUID} $user_id User to update
	 * @param {Integer} $timestamp Unix timestamp of the visit
	 * @return {Boolean} Update status
	 */
	public function setLastUpdate($user_id, $timestamp) {
		$datetime = date(MYSQL_DATE_FORMAT,$timestamp);
		return $this->updateAll(
			array("{$this->alias}.last_update" => '\''.$datetime.'\''),
			array(
				"{$this->alias}.{$this->primaryKey}" => $user_id,
				"{$this->alias}.last_update <" => $datetime
			)
		);
	}
	
	/**
	 * The users currently logged in (based on the last activity). Results are
	 * cached as this is often requested, yet infrequently changes.
	 *
	 * @return {Array} User model data of active users
	 */
	public function getOnlineUsers() {
		$users = Cache::read('onlineUsers','online_status');
		
		// cache-miss
		if($users === false) {
			$this->attachTimeDeltas = true;
			$users = $this->find('all',array(
				'fields'=>array('username','last_ack'),
				'conditions'=>array(
					'last_seen >='=>date(MYSQL_DATE_FORMAT, strtotime('now - 2 minutes'))
				)
			));
			$users = Hash::remove($users, '{n}.User.last_ack');
			Cache::write('onlineUsers', $users, 'online_status');
		}
		
		return $users;
	}

	/**
	 * Clears cache related to active user state. Should be ran after new logins
	 * logouts, etc so the user stats are as accurate as possible.
	 */
	public function resetUserCache() {
		Cache::delete('onlineUsers', 'online_status');
		Cache::gc('online_status');
	}
	
	/**
	 * Obtains the Facebook SDK object, used for User interactions with the 
	 * Facebook service.
	 * 
	 * @return {Facebook object | false}
	 */
	public function getFacebookObject() {
		if($this->facebookObj !== false) {
			return $this->facebookObject;
		}
		
		App::import('Vendor', 'Facebook-PHP-SDK/src/facebook');
		$settings = $this->_getFacebookSettings();
		
		if(!class_exists('Facebook') || $settings === false) {
			$this->log('Could not create a Facebook SDK object');
			return false;
		}
		
		$this->facebookObj = new Facebook($settings);
		return $this->facebookObj;
	}
	
	/**
	 * Obtains the configuration settings for the Facebook SDK
	 *
	 * @return {Array | fase} Settings array or false on failure
	 */
	protected function _getFacebookSettings() {
		try {
			Configure::load('facebook');
		} catch (ConfigureException $e) {
			$this->log('Could not load the Facebook app settings');
			return false;
		}
		
		return array(
			'appId'  => Configure::read('FB_App.id'),
			'secret' => Configure::read('FB_App.secret'),
			'fileUpload' => true
		);
	}
	
	/**
	 * Returns the minimum necessary FB user permissions needed to properly
	 * integrate site features with the service.
	 *
	 * https://developers.facebook.com/docs/authentication/permissions/
	 * 
	 * @return {Array} Facebook user permissions
	 */
	public function getFacebookPermissions() {
		return array(
			'user_groups', 'publish_stream'
		);
	}

	/**
	 * Obtains the key used in session persistence for this specific user
	 *
	 * @param {UUID} $user_id User primary key
	 * @return {String|false} Identifier key or false on error
	 */
	public function getSessionIdentifier($user_id) {

		if(!$this->exists($user_id)) {
			return false;
		}

		$this->id = $user_id;
		$key = $this->field('session_key');

		// set a new key if one does not exist
		if(empty($key)) {
			$key = String::uuid();
			$this->saveField('session_key', $key);
		}

		return $key;
	}

	/**
	 * Obtains a user record from a session identifier
	 *
	 * @param {String} $identifier Unique user session identifier
	 * @return {User} Matching user record
	 */
	public function getBySessionIdentifier($identifier) {
		return $this->findBySessionKey($identifier);
	}
}
