<?php
App::uses('AppController', 'Controller');
/**
 * Manages a site's user accounts and authentication
 * @property User $User
 */
class UsersController extends AppController {

	public $paginate = array(
		'Activity' => array(
			'contain' => array(
				'User', 'Asset', 'Link', 'Video',
				'Album' => array(
					'AssetCount', 'DefaultCover', 'Cover'
				),
				'Contest' => 'Asset',
				'Message' => array('Asset', 'Contest' => 'Asset', 'Link', 'Video')
			),
			'limit' => 15
		)
	);

	public $uses = array('User', 'Message', 'Activity', 'Link', 'Album');

	public $restrictedRoutes = array('admin_index', 'admin_add', 'admin_view', 'admin_delete', 'admin_refresh_updates');

	public function adminBeforeFilter() {
		parent::adminBeforeFilter();
		$this->Auth->allow(array('admin_login', 'admin_setup', 'admin_forgot'));
	}

	public function admin_login() {
		if($this->Auth->user('id')) {
			$this->redirect($this->Auth->redirectUrl());
		}

		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				$this->User->setLastLogin($this->Auth->user('id'), Configure::read('App.start'));
				$this->User->setLastSeen($this->Auth->user('id'), Configure::read('App.start'));
				$this->User->resetUserCache();
				$this->persistSession();
				$this->redirect($this->Auth->redirectUrl());
			} else {
				$this->Session->setFlash('Invalid username or password, try again', 'messaging/alert-error');
			}
		}
	}

	/**
	 * User landing page with a number of app content summaries
	 */
	public function admin_dashboard() {
		$users = $this->User->find('all', array(
			'order' => 'last_seen DESC',
			'limit' => 5
		));
		$asset_count = $this->User->Asset->find('count', array(
			'conditions' => array(
				'user_id' => $this->Auth->user('id')
			)
		));
		$asset_count_all = $this->User->Asset->find('count');
		$this->set('recent_users', $users);
		$this->set('meme_count', $this->User->Asset->getCleanImageCount());
		$this->set('contest_count', ClassRegistry::init('Contest')->find('count'));
		$this->set('quotes_count', ClassRegistry::init('Post')->find('count'));
		$this->set('links_count', ClassRegistry::init('Link')->find('count'));
		$this->set('videos_count', ClassRegistry::init('Video')->find('count', array('conditions' => array('mp4' => 1))));
		$this->set('album_count', ClassRegistry::init('Album')->getAlbumCount($this->Auth->user('id')));
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
		$view_all = (isset($this->request->params['named']['view']) && $this->request->params['named']['view'] === 'all');
		if(!$view_all) {
			$this->paginate['Activity']['conditions']['Activity.user_id <>'] = $this->Auth->user('id');
		}
		$this->set('updates', $this->paginate('Activity'));
		$this->set('page_limits', array(15, 30, 60));
	}

	/**
	 * Admin utility function to re-create the Activity data set.
	 */
	public function admin_refresh_updates() {
		if(Access::hasRole('Admin')) {
			ClassRegistry::init('Asset')->refreshPostableIndex();
			ClassRegistry::init('Contest')->refreshPostableIndex();
			ClassRegistry::init('Message')->refreshPostableIndex();
		}
		$this->redirect($this->referer(array('action' => 'admin_updates')));
	}

	/**
	 * Terminate a user session
	 */
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
	 * Tracks periodic "live" status of the user, returning application state
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
	 * Site administrator view of all User accounts
	 */
	public function admin_index() {
		$this->set('users', $this->paginate('User'));
	}

	/**
	 * Site administrator review of a single User's account
	 */
	public function admin_view($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->set('user', $this->User->read(null, $id));
	}

	/**
	 * Add a new User account
	 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->User->create();
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash('The user has been saved', 'messaging/alert-success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('The user could not be saved. Please, try again.', 'messaging/alert-error');
			}
		}
	}

	/**
	 * Allows a user to edit their account with a shorter URL
	 */
	public function admin_account() {
		$this->setAction('admin_edit', $this->Auth->user('id'));
	}

	/**
	 * User account update.
	 */
	public function admin_edit($id = null) {
		$this->User->id = $id;

		// non admins can't edit other Users
		if(!Access::hasRole('Admin') && !Access::isOwner($id)) {
			$this->redirect($this->userHome);
		}

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
				if(Access::isOwner($id)) {
					$msg = 'Your account has been updated.';
					$self = $this->User->findById($id);
					$this->Auth->login($self['User']);

				} else {
					$msg = 'The user has been updated.';
				}
				$this->Session->setFlash($msg, 'messaging/alert-success');
				$this->redirect($this->referer($this->userHome));
			} else {
				$msg = Access::isOwner($id) ? 'Your account could not be saved. Please, try again.' : 'The user could not be saved. Please, try again.';
				$this->Session->setFlash($msg,'messaging/alert-error');
			}
		} else {
			$this->request->data = $this->User->read(null, $id);
		}
	}

	/**
	 * Augments the user edit form with Facebook integration data
	 */
	public function admin_fb_groups() {
		if($this->User->hasFacebookAccess()) {

			$groups = $this->User->getFacebookUserGroups();
			if(is_array($groups)) {
				 $group_lookup = Hash::combine($groups, '{n}.id', '{n}.name');

				// restrict the allowable group associations based on app-level whitelists
				$whitelist = $this->User->getWhitelistedGroups();
				if(is_array($whitelist)) {
					$group_lookup = array_intersect_key($group_lookup, array_flip($whitelist));
				}
				$this->set('groups', $group_lookup);
			}

			$this->setAction('admin_edit', $this->Auth->user('id'));

		} else {
			$redirect_url = $this->User->getFacebookLoginUrl(Router::url(array('controller' => 'users', 'action' => 'fb_groups'), true));
			$this->redirect($redirect_url);
		}
	}

	/**
	 * Terminates a User's account
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
			$this->Session->setFlash('User deleted', 'messaging/alert-success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('User was not deleted', 'messaging/alert-error');
		$this->redirect(array('action' => 'index'));
	}

	/**
	 * Allow a user to send a password reset
	 */
	public function admin_forgot() {
		if ($this->request->is('post')) {
			$this->User->set($this->request->data);
			$this->User->validator()->remove('email', 'isUnique');
			if($this->User->validates(array('fieldList' => array('email')))) {

				$user = $this->User->getActiveByEmail($this->request->data('User.email'));

				if(!empty($user)) {
					$token = '1234567890';
					$this->sendResetEmail($user, $token);
					$this->Session->setFlash('A password reset email has been sent!', 'messaging/alert-success');
				} else {
					$this->Session->setFlash('User not found', 'messaging/alert-error');
				}
			}
		}
	}

	/**
	 * Sends a password reset email
	 *
	 * @param array $user
	 * @param string $token
	 */
	protected function sendResetEmail($user, $token) {
		App::uses('CakeEmail', 'Network/Email');
		$email = new CakeEmail('default');

		$site_name = Configure::read('Site.name');

		$email->template('password_reset', 'default');
		$email->emailFormat('html');
		$email->to($user['User']['email']);
		$email->subject('Password reset for ' . $site_name);
		$email->viewVars(array(
			'title_for_layout' => $email->subject(),
			'site_name' => $site_name,
			'reset_url' => Router::url(array('controller' => 'users', 'action' => 'reset', 'token' => $token), true)
		));

		$email->send();
	}
}
