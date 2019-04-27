<?php
App::uses('Controller', 'Controller');
App::uses('CakeNumber', 'Utility');
App::uses('Access', 'Model');

class AppController extends Controller
{
	public $uses = array('User');

	public $helpers = array(
		'Site',
		'Cache',
		'Form' => array('className' => 'AppForm'),
		'Html' => array('className' => 'AppHtml'),
		'Flash',
	);

	public $components = array(
		'RequestHandler',
		'Session',
		'Cookie',
		'Auth' => array(
			'loginRedirect' => array('controller' => 'users', 'action' => 'dashboard'),
			'authError' => 'You must be logged in to continue',
			'flash' => array(
				'element' => 'alert',
				'key' => 'auth',
				'params' => array()
			)
		),
		'Security' => array(
			'csrfCheck' => false
		),
		'Flash' => array(
			'element' => 'alert',
		),
	);

	public $userHome = '/';

	// routes reserved for high-level roles only
	public $restrictedRoutes = array();

	public function beforeFilter()
	{
		$this->addRequestDetector();
		$this->setSecurity();

		if($this->request->is('backend')) {
			$this->adminBeforeFilter();
		} else {
			$this->siteBeforeFilter();
		}
		parent::beforeFilter();
	}

	public function siteBeforeFilter()
	{
		$this->Auth->allow();

		// compress all output
		$this->response->compress();
	}

	public function adminBeforeFilter()
	{
		$this->layout = 'admin';
		$this->userHome = array('controller' => 'users', 'action' => 'dashboard');

		// restrict certain actions to higher-level roles
		if(!empty($this->restrictedRoutes) && !Access::hasRole('Admin') && in_array($this->request->params['action'], $this->restrictedRoutes)) {
			$this->redirect($this->userHome);
		}

		$this->handleAutoLogin();
		$this->detectAjax();
	}

	public function beforeRender()
	{
		if(!$this->request->is('ajax')) {
			$this->set('breadcrumbs', array());
			$this->set('contentSpan', 8);
			$this->set('onlineUsers', $this->User->getOnlineUsers());
		}

		if($this->request->is('backend')) {
			$this->adminBeforeRender();
		}
	}

	/**
	 * Pre-view generation processing for authenticated users
	 */
	public function adminBeforeRender()
	{
		$this->set('siteName', Configure::read('Site.name'));
		$this->set('backend', Configure::read('Site.backendUrl'));
		$this->set('userHome', $this->userHome);

		$this->helpers[] = 'Snippet';
	}

	/**
	 * Post-processing which is not specific to the generated response
	 */
	public function afterFilter()
	{
		if($this->Auth->loggedIn()) {

			// track the time of the last activity from a specific user
			$this->User->setLastSeen($this->Auth->user('id'), Configure::read('App.start'));
		}
	}

	/**
	 * Configures settings relating to overall app security
	 */
	protected function setSecurity()
	{
		Security::setHash('sha256');
		$this->Cookie->name = Configure::read('Cookie.name');
		$this->Cookie->type('rijndael');
		$this->Cookie->key = Configure::read('Cookie.key');
		$this->Cookie->httpOnly = true;
	}

	/**
	 * Add custom request detections specific to the app
	 */
	protected function addRequestDetector()
	{
		// allow easy detection of the user portal pages
		$this->request->addDetector('backend', array(
			'callback' => function ($request) {
				return !empty($request->params['prefix'])
					&& $this->request->params['prefix'] == 'admin'
					&& $this->request->params['admin'] == '1';
			}
		));
	}

	/**
	 * Detect returning users and log them in
	 */
	protected function handleAutoLogin()
	{
		// feature must be enabled
		if(Configure::read('Site.Tracking.RememberMe.enabled') !== true) {
			return;
		}

		// can't already be logged in, or executing a POST
		if($this->Auth->loggedIn() || $this->request->isPost()) {
			return;
		}

		// Cookie value must exist
		$user_key = $this->Cookie->read('persist');
		if(empty($user_key)) {
			return;
		}

		$user = $this->User->getBySessionIdentifier($user_key);

		// re-authentication failed
		if(!$user || !$this->Auth->login($user['User'])) {
			$this->Cookie->delete('persist');

		// re-authentication succeeds
		} else {
			$this->postLogin();
		}
	}

	/**
	 * Compiles the app-state packet used by the front-end to alert the user to
	 * any unseen activity, and overall app statistics.
	 *
	 * This packet of information should remain lightweight as it is drawn
	 * frequently to give live-like response times.
	 */
	protected function _getHeartbeatData()
	{
		$MessageModel = ClassRegistry::init('Message');
		$currentUser = $this->Auth->user('id');

		$data = array();
		$data['online'] = $this->User->getOnlineUsers();
		$data['ack'] = time();

		// process the acknowledgement packet (TCP-like), updating stored user state
		if(isset($this->request->query['ack'])) {
			$clientAck = (int)$this->request->query['ack'];
			$model = $this->request->query['scope'];
			$foreign_key = (!empty($this->request->query['key'])) ? $this->request->query['key'] : false;

			$options = array();

			if($model == 'Chat') {
				$options['since'] = date(MYSQL_DATE_FORMAT, max($clientAck, $MessageModel->minimumSince));
				$options['limit'] = Configure::read('Site.Chat.maxHistoryCount');

				// store the acknowledgement timestamp, limiting wasteful re-sending of history
				if($clientAck !== 0) {
					$this->User->setLastAck($currentUser, $clientAck);
				}
			}

			// pull chat messages the user has not yet seen (all for first visits)
			$data['messages'] = $MessageModel->getNewMessages($model, $foreign_key, $options);
		}

		// count the chat messages a user has not yet seen
		$data['new_messages'] = $MessageModel->countNewMessages('Chat', $currentUser);

		// cap the message count if it goes beyond the max buffer size
		$maxHistoryCount = Configure::read('Site.Chat.maxHistoryCount');
		if($data['new_messages'] > $maxHistoryCount) {
			$data['new_messages'] = $maxHistoryCount;
		}

		// pull the count of new activity updates for the user
		$data['new_updates'] = ClassRegistry::init('Activity')->countNewActivity($currentUser);

		return $data;
	}

	/**
	 * Persists a user's session after login for repeat visits.
	 */
	protected function persistSession()
	{
		if(Configure::read('Site.Tracking.RememberMe.enabled') !== true) {
			return;
		}

		$identifier = $this->User->getSessionIdentifier($this->Auth->user('id'));
		if($identifier !== false) {
			// store user information in an encrypted cookie
			$this->Cookie->write('persist', $identifier, true, Configure::read('Site.Tracking.RememberMe.expiry'));
		}
	}

	/**
	 * Post login routine to maintain user & system state
	 */
	protected function postLogin()
	{
		$this->User->setLastLogin($this->Auth->user('id'), Configure::read('App.start'));
		$this->User->setLastSeen($this->Auth->user('id'), Configure::read('App.start'));
		$this->User->resetUserCache();
		$this->persistSession();
	}

	/**
	 * Detects if an AJAX request is in progress, allowing it to pass
	 */
	protected function detectAjax()
	{
		if($this->Auth->loggedIn() && $this->request->is('ajax')) {
			$this->disableCache(); // expire cache immediately
			$this->RequestHandler->renderAs($this, 'json');
			$this->Security->validatePost = false;
			$this->Security->csrfCheck = false;
		}
	}

	/**
	 * Utility function allowing a force-reindexing of Models controlled by the
	 * Postable behavior. This can serve as a maintenance function for admin
	 * users.
	 */
	public function admin_refresh_model()
	{
		if(Access::hasRole('Admin') && $this->{$this->modelClass}->Behaviors->attached('Postable')) {
			$this->{$this->modelClass}->refreshPostableIndex();
		}
		$this->redirect($this->referer());
	}
}
