<?php
App::uses('AppModel', 'Model');
App::uses('AuthComponent', 'Controller/Component');
/**
 * Manages interactions with site user accounts
 * @property Token $PasswordToken
 */
class User extends AppModel {

	public $displayField = 'username';

	const TOKEN_PASSWORD_RESET = 'password_reset';
	const TOKEN_EXPIRY = '+1 day';

	public $hasOne = array(
		'PasswordToken' => array(
			'className' => 'Token',
			'foreignKey' => 'foreign_id',
			'conditions' => array(
				'PasswordToken.type' => self::TOKEN_PASSWORD_RESET,
				'PasswordToken.expiry <= NOW()'
			),
			'dependent' => true
		)
	);

	public $hasMany = array('Asset', 'Contest');
	public $attachTimeDeltas = false;
	public $actsAs = array('Facebook');

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
		'email' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Email is required',
				'required' => true
			),
			'formatting' => array(
				'rule' => '/^\S+@\S+$/',
				'message' => 'Invalid email provided.',
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
		// hash password
		if (!empty($this->data[$this->alias]['password'])) {
			$this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
		}

		if (isset($this->data[$this->alias]['fb_target']) && $this->data[$this->alias]['fb_target'] == '') {
			$this->data[$this->alias]['fb_target'] = null;
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
			array("{$this->alias}.last_login" => '\'' . date(MYSQL_DATE_FORMAT, $timestamp) . '\''),
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
			array("{$this->alias}.last_seen" => '\'' . date(MYSQL_DATE_FORMAT, $timestamp) . '\''),
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
			array("{$this->alias}.last_ack" => '\'' . $datetime . '\''),
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
			array("{$this->alias}.last_update" => '\'' . $datetime . '\''),
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
			$users = $this->find('all', array(
				'fields' => array('username','last_ack'),
				'conditions' => array(
					'last_seen >=' => date(MYSQL_DATE_FORMAT, strtotime('now - 2 minutes'))
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

	/**
	 * Retrieve an active user by their email address
	 *
	 * @param string $email
	 * @return array|null
	 */
	public function getActiveByEmail($email) {
		return $this->find('first', array(
			'conditions' => array(
				'email' => $email,
			)
		));
	}

	/**
	 * Makes or resets a 'forgot password' token
	 *
	 * @param string $user_id
	 * @return array
	 */
	public function generatePasswordResetToken($user_id)
	{
		$this->PasswordToken->removeAllByLookup($user_id, static::TOKEN_PASSWORD_RESET);
		return $this->PasswordToken->addNewToken(
			$user_id,
			self::TOKEN_EXPIRY,
			self::TOKEN_PASSWORD_RESET
		);
	}
}
