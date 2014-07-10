<?php
/**
 * Adds Facebook integration capabilities to a Model
 */
class FacebookBehavior extends ModelBehavior {

	protected $facebookObj = false;

	/**
	 * Verifies if a the SDK has proper User credentials
	 *
	 * @return {Boolean}
	 */
	public function hasFacebookAccess() {
		$sdk = $this->getFacebookObject();
		return $sdk->getUser() !== 0;
	}

	/**
	 * Obtains the Facebook SDK object, used for User interactions with the
	 * Facebook service.
	 *
	 * @return {Facebook object | false}
	 */
	public function getFacebookObject() {
		if($this->facebookObj !== false) {
			return $this->facebookObj;
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
	 * Allows API calls through the SDK
	 *
	 * @param {String} $endpoint Absolute URI for the desired API call
	 * @param {String} $type HTTP verbage, ex: GET, POST, PUT
	 * @param {Array} $params Optional parameters
	 * @return {Array|false}
	 */
	public function facebookApiCall(Model $model, $endpoint, $type = 'GET', $params = array()) {
		$sdk = $this->getFacebookObject();

		try {
			return $sdk->api($endpoint, $type, $params);
		} catch(FacebookApiException $e) {
			$this->log("FB API Exception for {$endpoint}");
			$this->log($e->getType());
			$this->log($e->getMessage());
			return false;
		};
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
			'user_groups', 'publish_actions'
		);
	}

	/**
	 * Generates an OAuth URL which obtains User-level permissions.
	 *
	 * @param {String} $redirect_url Callback URL once the user authenticates
	 * @return {String} Facebook URL to authenticate the user
	 */
	public function getFacebookLoginUrl(Model $model, $redirect_url) {
		$sdk = $this->getFacebookObject();

		$login_params = array(
			'scope' => $this->getFacebookPermissions(),
			'redirect_uri' => $redirect_url
		);

		return $sdk->getLoginUrl($login_params);
	}

	/**
	 * Obtains a User's Group memberships
	 */
	public function getFacebookUserGroups() {
		$sdk = $this->getFacebookObject();
		$user_id = $sdk->getUser();
		$groups = array();

		if($user_id) {
			try {
				$result = $sdk->api("/{$user_id}/groups", 'GET');
				if(!empty($result['data'])) {
					$groups = $result['data'];
				}
			} catch (FacebookApiException $e) {
				$this->log('Error fetching User Group associations for ' . $user_id);
			}
		}

		return $groups;
	}

	/**
	 * Obtains the configurable list of comma-separated group IDs which are permitted
	 * in app integration and notifications.
	 */
	public function getWhitelistedGroups() {
		$groups = Configure::read('FB_App.group_whitelist');
		if(is_string($groups)) {
			return explode(',', $groups);
		}
		return null;
	}

}