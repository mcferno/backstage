<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');
App::uses('CakeNumber', 'Utility');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 */
class AppController extends Controller {
	
	public $uses = array('User');
	
	public $helpers = array(
		'Site', 'Cache',
		'Form' => array('className' => 'AppForm'),
		'Html' => array('className' => 'AppHtml')
	);
	
	public $components = array(
		'RequestHandler', 'Session', 'Cookie',
		'Auth' => array(
			'loginRedirect' => array('controller' => 'users', 'action'=>'dashboard'),
			'authError' => 'You must be logged in to continue',
			'flash' => array(
				'element' => 'messaging/alert',
				'key' => 'auth',
				'params' => array()
			)
		),
		'Security' => array(
			'csrfCheck' => false
		)
	);
	
	public function beforeFilter() {
		$this->setSecurity();
		
		if(isset($this->request->params['prefix']) 
		&& $this->request->params['prefix'] == 'admin'
		&& $this->request->params['admin'] == '1') {
			$this->adminBeforeFilter();
		} else {
			$this->siteBeforeFilter();
		}
		parent::beforeFilter();
	}
	
	public function siteBeforeFilter() {
		$this->Auth->allow();
		
		// compress all output
		$this->response->compress();
	}
	
	public function adminBeforeFilter() {
		$this->layout = 'admin';

		// attempt a "remember me"
		if(!$this->request->isPost() && !$this->Auth->loggedIn() && $this->Cookie->read('persist')) {

			$user_key = $this->Cookie->read('persist');
			if(!empty($user_key)) {
				$user = $this->User->getBySessionIdentifier($user_key);

				// re-authentication failed
				if(!$user || !$this->Auth->login($user['User'])) {
					$this->Cookie->delete('persist');

				// re-authentication succeeds
				} else {
					$this->User->setLastLogin($this->Auth->user('id'), Configure::read('App.start'));
					$this->User->setLastSeen($this->Auth->user('id'), Configure::read('App.start'));
					$this->User->resetUserCache();
					$this->persistSession();
				}
			}
		}

		$this->detectAjax();
	}
	
	public function beforeRender() {
		if(!$this->request->is('ajax')) {
			$this->set('breadcrumbs',array());
			$this->set('contentSpan',8);
			$this->set('onlineUsers',$this->User->getOnlineUsers());
		}

		if(isset($this->request->params['prefix']) 
		&& $this->request->params['prefix'] == 'admin'
		&& $this->request->params['admin'] == '1') {
			$this->adminBeforeRender();
		}
	}

	public function adminBeforeRender() {
	}
	
	/**
	 * Post-processing which should not hold up a request
	 */
	public function afterFilter() {
		if($this->Auth->loggedIn()) {
			
			// track the time of the last activity from a specific user
			$this->User->setLastSeen($this->Auth->user('id'), Configure::read('App.start'));
		}
	}

	/**
	 * Configures settings relating to overall app security
	 */
	protected function setSecurity() {
		Security::setHash('sha256');
		$this->Cookie->name = 'KQM';
		$this->Cookie->type('rijndael');
		$this->Cookie->key = Configure::read('Cookie.key');
		$this->Cookie->httpOnly = true;
	}
	
	/**
	 * Determines if the passed key matches that of the currently authenticated
	 * user.
	 *
	 * @param {String} $id
	 * @return {Boolean} Whether the id matches the current auth user
	 */
	protected function _isUser($id) {
		return $this->Auth->user('id') === $id;
	}
	
	/**
	 * Set of data needed by the front-end application to maintain state and 
	 * user interactivity.
	 */
	protected function _getHeartbeatData() {
		$MessageModel = ClassRegistry::init('Message');
		$currentUser = $this->Auth->user('id');
		
		$data = array();
		$data['online'] = $this->User->getOnlineUsers();
		$data['ack'] = time();
		
		if(isset($this->request->query['ack'])) {
			$clientAck = (int)$this->request->query['ack'];
			$model = $this->request->query['scope'];

			// set the last ack to no more than 24 hrs ago
			$since = date(MYSQL_DATE_FORMAT, max($clientAck, $MessageModel->minimumSince));
			
			$foreign_key = (!empty($this->request->query['key'])) ? $this->request->query['key'] : false;

			// user's first request
			if($clientAck === 0) {

				// eliminate message cap on non-chat interfaces
				if($model != 'Chat') {
					$since = false;
				}
				$data['messages'] = $MessageModel->getNewMessages($model, $foreign_key, $since);

			// follow-up request (exclude one's own messages)
			} else {

				// if on Chat, update the ack value for future revisits, and notification supression
				if($model == 'Chat') {
					$this->User->setLastAck($currentUser, $clientAck);
				}
				$data['messages'] = $MessageModel->getNewMessages($model, $foreign_key, $since, $currentUser);
			}
		}
		
		$data['new_messages'] = $MessageModel->countNewMessages('Chat', $currentUser);
		$data['new_updates'] = ClassRegistry::init('Activity')->countNewActivity($currentUser);
		
		return $data;
	}

	/**
	 * Persists a user's session after login for repeat visits.
	 */
	protected function persistSession() {

		$identifier = $this->User->getSessionIdentifier($this->Auth->user('id'));
		
		if($identifier !== false) {
			// store user information in an encrypted cookie
			$this->Cookie->write('persist', $identifier, true, '+1 month');
		}
	}

	/**
	 * Determines if the current User is classified as a site administrator
	 *
	 * @return {Boolean}
	 */
	protected function isAdminUser($min_role = ROLES_ADMIN) {
		return $this->Session->check('Auth.User.role') && (int)$this->Auth->user('role') >= $min_role;
	}

	/**
	 * Detects if an AJAX request is in progress, allowing it to pass
	 */
	protected function detectAjax() {
		if($this->Auth->loggedIn() && $this->request->is('ajax')) {
			$this->disableCache(); // expire cache immediately
			$this->RequestHandler->renderAs($this, 'json');
			$this->Security->validatePost = false;
			$this->Security->csrfCheck = false;
		}
	}

	public function admin_refresh_model() {
		if($this->isAdminUser() && $this->{$this->modelClass}->Behaviors->attached('Postable')) {
			$this->{$this->modelClass}->refreshPostableIndex();
		}
		$this->redirect($this->referer());
	}
}
