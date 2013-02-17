<?php
App::import('CakeSession', 'Model');
/**
 * Static User Session detection class
 */
class Access {

	/**
	 * All recognized user roles, some are inferred by a number of conditions
	 */
	public static $roles = array(
		'Public'	=> 0,
		'User'		=> 10,
		'Manager'	=> 20,
		'Admin'		=> 30
	);

	/**
	 * Role levels directly assignable to a User record
	 */
	public static $assignableRoles = array(
		0 => 'User',
		1 => 'Admin'
	);

	// in-memory cache for repeat requests
	private static $cache = array();

	/**
	 * Determines the role of the requesting User
	 */
	public static function getRole() {

		if(isset(self::$cache['role'])) {
			return self::$cache['role'];
		}

		// everyone is assumed as public until further tests
		$role = self::$roles['Public'];

		// user is authenticated
		if(CakeSession::check('Auth.User.id')) {

			// User is of the admin group
			if(CakeSession::check('Auth.User.role') && intval(CakeSession::read('Auth.User.role')) === 1) {
				$role = self::$roles['Admin'];

			// Standard user account
			} else {
				$role = self::$roles['User'];
			}
		}

		self::$cache['role'] = $role;
		return $role;
	}

	/**
	 * Determines if the User matches the provided ID
	 *
	 * @return {Boolean}
	 */
	public static function isOwner($user_id) {

		if(!CakeSession::check('Auth.User.id')) {
			return false;
		}

		return CakeSession::read('Auth.User.id') === $user_id;
	}
	
	/**
	 * Determines whether the current User has a certain role level. By default,
	 * this check allows higher roles access as well. Use strict to ensure the
	 * User has exactly the requested role.
	 *
	 * @param {String} $roleName The role used for comparison against the User's role level
	 * @param {Boolean} $strict Exact match on role (true), or allow equal or greater roles (false)
	 * @return {Boolean} Whether the current User has the required role level
	 */
	public static function hasRole($roleName, $strict = false) {

		// misconfigured request, throw an error
		if(!isset(self::$roles[$roleName])) {
			throw new Exception("The requested User role {$roleName} does not exist.");
		}

		$userRole = self::getRole();

		if($strict === true) {
			return $userRole === self::$roles[$roleName];
		}

		return $userRole >= self::$roles[$roleName];
	}
}