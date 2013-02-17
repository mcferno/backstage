<?php
App::uses('AppController', 'Controller');
/**
 * Users Controller
 *
 * @property User $User
 */
class UsersController extends AppController {

	public $paginate = array(
		'Activity' => array(
			'contain' => array(
				'User', 'Asset', 'Link','Video',
				'Contest' => 'Asset', 
				'Message' => array('Asset', 'Contest' => 'Asset', 'Link', 'Video')
			),
			'limit' => 15
		)
	);
	
	public $uses = array('User', 'Message', 'Activity', 'Link');
	
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
				$this->User->setLastLogin($this->Auth->user('id'), Configure::read('App.start'));
				$this->User->setLastSeen($this->Auth->user('id'), Configure::read('App.start'));
				$this->User->resetUserCache();
				$this->persistSession();
				$this->redirect($this->Auth->redirect());
			} else {
				$this->Session->setFlash('Invalid username or password, try again','messaging/alert-error');
			}
		}
	}
	
	/**
	 * User landing page
	 */
	public function admin_dashboard() {
		$users = $this->User->find('all',array(
			'order'=>'last_seen DESC',
			'limit'=>7
		));
		$asset_count = $this->User->Asset->find('count',array(
			'conditions'=>array(
				'user_id'=>$this->Auth->user('id')
			)
		));
		$asset_count_all = $this->User->Asset->find('count');
		$this->set('recent_users', $users);
		$this->set('meme_count', count(glob(IMAGES.'base-meme'.DS.'*.*')));
		$this->set('contest_count', ClassRegistry::init('Contest')->find('count'));
		$this->set('quotes_count', ClassRegistry::init('Post')->find('count'));
		$this->set('links_count', ClassRegistry::init('Link')->find('count'));
		$this->set('videos_count', ClassRegistry::init('Video')->find('count', array('conditions' => array('mp4' => 1))));
		$this->set('asset_count', $asset_count);
		$this->set('asset_count_all', $asset_count_all);

		// obtain a subset of the latest updates
		$this->paginate['Activity']['conditions']['Activity.user_id <>'] = $this->Auth->user('id');
		$this->paginate['Activity']['limit'] = 5;
		$this->set('updates', $this->paginate('Activity'));
	}

	/**
	 * User system updates, the list of user actions across site features.
	 */
	public function admin_updates() {
		$this->User->setLastUpdate($this->Auth->user('id'), Configure::read('App.start'));
		$this->paginate['Activity']['conditions']['Activity.user_id <>'] = $this->Auth->user('id');
		$this->set('updates', $this->paginate('Activity'));
		$this->set('page_limits', array(15, 30, 60));
	}

	/**
	 * Admin utility function to re-create the Activity data set.
	 */
	public function admin_refresh_updates() {
		if($this->isAdminUser()) {
			ClassRegistry::init('Asset')->refreshPostableIndex();
			ClassRegistry::init('Contest')->refreshPostableIndex();
			ClassRegistry::init('Message')->refreshPostableIndex();
		}
		$this->redirect($this->referer(array('action' => 'admin_updates')));
	}
	
	public function admin_logout() {
		$this->Cookie->delete('persist');
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
	 * Tracks periodic "live" status of the user, returning application 'state'
	 */
	public function admin_heartbeat() {
		$this->cacheAction = false;
		$this->disableCache(); // expire cache immediately
		$this->RequestHandler->renderAs($this, 'json');
		
		$data = $this->_getHeartbeatData();
		
		$this->set($data);
		$this->set('_serialize', array_keys($data));
	}
	
	/**
	 * Group chat interface
	 */
	public function admin_group_chat() {
		$this->User->setLastAck($this->Auth->user('id'), Configure::read('App.start'));
	}
	
	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index() {
		$this->set('users', $this->paginate('User'));
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
				$msg = Access::isOwner($id) ? 'Your account has been updated.' : 'The user has been updated.';
				$this->Session->setFlash($msg,'messaging/alert-success');
				$this->redirect($this->referer(array('action' => 'index')));
			} else {
				$msg = Access::isOwner($id) ? 'Your account could not be saved. Please, try again.' : 'The user could not be saved. Please, try again.';
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
