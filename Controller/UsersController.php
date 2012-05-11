<?php
App::uses('AppController', 'Controller');
/**
 * Users Controller
 *
 * @property User $User
 */
class UsersController extends AppController {
	public $paginate = array(
		'User'=>array()
	);
	
	public $uses = array('User','Message');
	
	public function adminBeforeFilter() {
		parent::adminBeforeFilter();
		$this->Auth->allow(array('admin_login', 'admin_setup'));
	}

	public function admin_login() {
		if($this->Auth->user('id')) {
			$this->redirect($this->Auth->redirect());
		}
		
		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				$this->User->setLastLogin($this->Auth->user('id'),time());
				$this->redirect($this->Auth->redirect());
			} else {
				$this->Session->setFlash('Invalid username or password, try again','messaging/alert-error');
			}
		}
	}
	
	public function admin_dashboard() {
		$users = $this->User->find('all',array(
			'order'=>'created DESC',
			'limit'=>5
		));
		$asset_count = $this->User->Asset->find('count',array(
			'conditions'=>array(
				'user_id'=>$this->Auth->user('id')
			)
		));
		$asset_count_all = $this->User->Asset->find('count');
		$this->set('recent_users',$users);
		$this->set('meme_count',count(glob(IMAGES.'base-meme'.DS.'*.*')));
		$this->set('quotes_count',ClassRegistry::init('Post')->find('count'));
		$this->set('asset_count',$asset_count);
		$this->set('asset_count_all',$asset_count_all);
	}
	
	public function admin_logout() {
		$this->redirect($this->Auth->logout());
	}

	/**
	 * Only called during application setup, in order to create an initial user
	 **/
	public function admin_setup() {
		if(Configure::read('setup') != 1) {
			$this->redirect('/');
		}
		$this->admin_add();
	}
	
	/**
	 * Tracks periodic "live" status of the user, returning useful data
	 */
	public function admin_heartbeat() {
		$this->cacheAction = false;
		$this->disableCache(); // expire cache immediately
		$this->RequestHandler->renderAs($this, 'json');
		
		$data = $this->_getHeartbeatData();
		
		$this->set($data);
		$this->set('_serialize', array_keys($data));
	}
	
	public function admin_group_chat() {
		$this->User->setLastAck($this->Auth->user('id'),Configure::read('App.start'));
	}
	
	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index() {
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}

	/**
	 * admin_view method
	 *
	 * @param string $id
	 * @return void
	 */
	public function admin_view($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->set('user', $this->User->read(null, $id));
	}

	/**
	 * admin_add method
	 *
	 * @return void
	 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->User->create();
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash('The user has been saved','messaging/alert-success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('The user could not be saved. Please, try again.','messaging/alert-error');
			}
		}
	}

	/**
	 * admin_edit method
	 *
	 * @param string $id
	 * @return void
	 */
	public function admin_edit($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			
			// skip password manipulation if left blank
			if(empty($this->request->data['User']['password'])) {
				unset($this->request->data['User']['password']);
				unset($this->User->validate['password']);
			}
			if ($this->User->save($this->request->data)) {
				$msg = $this->_isUser($id)?'Your account has been updated.':'The user has been updated.';
				$this->Session->setFlash($msg,'messaging/alert-success');
				$this->redirect($this->referer(array('action' => 'index')));
			} else {
				$msg = $this->_isUser($id)?'Your account could not be saved. Please, try again.':'The user could not be saved. Please, try again.';
				$this->Session->setFlash($msg,'messaging/alert-error');
			}
		} else {
			$this->request->data = $this->User->read(null, $id);
		}
	}

	/**
	 * admin_delete method
	 *
	 * @param string $id
	 * @return void
	 */
	public function admin_delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->User->delete()) {
			$this->Session->setFlash('User deleted','messaging/alert-success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('User was not deleted','messaging/alert-error');
		$this->redirect(array('action' => 'index'));
	}
}
