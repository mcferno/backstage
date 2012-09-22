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
		'RequestHandler','Session',
		'Auth' => array(
			'loginRedirect' => array('controller' => 'users', 'action'=>'dashboard'),
			'authError' => 'You must be logged in to continue',
			'flash' => array(
				'element' => 'messaging/alert',
				'key' => 'auth',
				'params' => array()
			)
		)
	);
	
	public function beforeFilter() {
		Security::setHash('sha256');
		
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
		$this->Auth->allow('*');
		
		// compress all output
		$this->response->compress();
	}
	
	public function adminBeforeFilter() {
		$this->layout = 'admin';
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
		$UserModel = ClassRegistry::init('User');
		$currentUser = $this->Auth->user('id');
		
		$data = array();
		$data['online'] = $UserModel->getOnlineUsers();
		$data['ack'] = time();
		
		if(isset($this->request->query['ack'])) {
			$clientAck = (int)$this->request->query['ack'];
			if($clientAck === 0) {
				$since = date(MYSQL_DATE_FORMAT,strtotime('now - 1 day'));
				$data['messages'] = $MessageModel->getNewMessages($since);
			} else {
				$since = date(MYSQL_DATE_FORMAT,$clientAck);
				$UserModel->setLastAck($currentUser, $clientAck);
				$data['messages'] = $MessageModel->getNewMessages($since, $currentUser);
			}
		}
		
		$data['new_messages'] = $MessageModel->countNewMessages($currentUser);
		
		return $data;
	}
}
