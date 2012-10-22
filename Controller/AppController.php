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
		'Site','Html','Js','Paginator','Session','Text','Cache',
		'Form' => array(
			'className' => 'AppForm'
		)
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
					$this->User->setLastLogin($this->Auth->user('id'), time());
					$this->persistSession();
				}
			}
		}
	}
	
	public function beforeRender() {
		if(!$this->request->is('ajax')) {
			$this->set('breadcrumbs',array());
			$this->set('contentSpan',8);
			$this->set('onlineUsers',$this->User->getOnlineUsers());
		}
	}
	
	/**
	 * Post-processing which should not hold up a request
	 */
	public function afterFilter() {
		if($this->Auth->loggedIn()) {
			
			// track the time of the last activity from a specific user
			ClassRegistry::init('User')->setLastSeen($this->Auth->user('id'),Configure::read('App.start'));
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

			// set the last ack to no more than 24 hrs ago
			$since = date(MYSQL_DATE_FORMAT, max($clientAck, $MessageModel->minimumSince));

			// user's first request
			if($clientAck === 0) {
				$data['messages'] = $MessageModel->getNewMessages($since);

			// follow-up request (exclude one's own messages)
			} else {
				$this->User->setLastAck($currentUser, $clientAck);
				$data['messages'] = $MessageModel->getNewMessages($since, $currentUser);
			}
		}
		
		$data['new_messages'] = $MessageModel->countNewMessages($currentUser);
		
		return $data;
	}

	/**
	 * Captures a URL, saving the contents to a temporary file
	 *
	 * @param {String} $url HTTP/s url to a file
	 * @param {Array} $mimeTypes Permitted mime-types to verify after download
	 * @return {String | false} System path the to downloaded asset
	 */
	protected function saveURLtoTemp($url, $mimeTypes = false) {

		$tempFile = tempnam(TMP , 'urlsave_');
		$fileHandle = fopen($tempFile, "w");
		$result = false;

		if($fileHandle !== false) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
			curl_setopt($ch, CURLOPT_TIMEOUT, 15);
			curl_setopt($ch, CURLOPT_FILE, $fileHandle);
			
			// successful save from url
			if(curl_exec($ch) !== false) {

				fflush($fileHandle);
				fclose($fileHandle);

				// restrict the file mime-types if provided
				if($mimeTypes === false || in_array(strtolower(mime_content_type($tempFile)), $mimeTypes)) {
					$result = $tempFile;
				}

			// failure to curl, scrap the temp file
			} else {
				fclose($fileHandle);
				unlink($tempFile);
			}

			curl_close($ch);
		}

		return $result;
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

}
