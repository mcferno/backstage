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
	
	public $helpers = array(
		'Site','Html','Paginator','Session','Text','Cache',
		'Form' => array(
			'className' => 'AppForm'
		)
	);
	
	public $components = array(
		'RequestHandler','Security','Session',
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
		$this->set('breadcrumbs',array());
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
}
