<?php
/**
 * Static content controller
 */
class PagesController extends AppController {

	public $name = 'Pages';
	public $helpers = array('Html');
	public $uses = array('Page', 'Tumblr', 'Contest', 'Asset');
	public $scaffold = 'admin';

	// admin-only scaffolding
	public function beforeScaffold($method) {
		if(!Access::hasRole('Admin')) {
			$this->redirect(array('controller'=>'users', 'action' => 'dashboard'));
		}
		$this->set('schema', $this->Page->schema());
		return parent::beforeScaffold($method);
	}

	/**
	 * Displays a view
	 *
	 * @param mixed What page to display
	 */
	public function display() {
		$path = func_get_args();

		$count = count($path);
		if (!$count) {
			$this->redirect('/');
		}
		$page = $subpage = $title_for_layout = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}
		$this->set(compact('page', 'subpage', 'title_for_layout'));
		$this->render(implode('/', $path));
	}

	/**
	 * Presents the interface for the js-driven meme generator tool.
	 */
	public function admin_meme_generator() {

		$images = array();

		// meme of a specific user-uploaded image
		if(!empty($this->request->params['named']['asset'])) {
			$path = $this->Asset->getPath($this->request->params['named']['asset']);

			if(!empty($path)) {
				$images[] = $path;
			}

		// caption contest
		} elseif(!empty($this->request->params['named']['contest'])) {
			$contest = $this->Contest->getActiveContest($this->request->params['named']['contest']);

			if($contest === false) {
				$this->Session->setFlash('Sorry, the contest is either done, or could not be found.', 'messaging/alert-error');
				$this->redirect(array('controller' => 'contests', 'action' => 'index'));
			}

			$images[] = $this->Asset->getPath($contest['Asset']['id']);

			$this->set('contest', $contest);
		}

		// fallback allowing users to choose an image
		if(empty($images)) {

			$this->set('image_tags', $this->Asset->Tag->getListForModel('Asset'));
			$contributingUsers = $this->Asset->find('all',array(
				'contain' => array(
					'User' => array(
						'fields' => 'id, username'
					)
				),
				'conditions' => $this->Asset->getCleanImageConditions(),
				'fields' => 'id',
				'group' => 'Asset.user_id',
				'order' => 'User.username ASC'
			));
			$this->set('image_owners', Hash::combine($contributingUsers, '{n}.User.id', '{n}.User.username'));

		}

		$this->set('first_line', (!empty($this->request->query['first-line'])) ? $this->request->query['first-line'] : '' );
		$this->set('last_line', (!empty($this->request->query['last-line'])) ? $this->request->query['last-line'] : '' );
		$this->set('base_images', $images);
	}

	/**
	 * Clears the view cache.
	 */
	public function admin_clear_cache() {
		if(Access::hasRole('Admin')) {
			if(clearCache() === true) {
				$msg = 'View cache has been cleared successfully!';
				$type = 'messaging/alert-success';
			} else {
				$msg = 'View cache could not be cleared!';
				$type = 'messaging/alert-error';
			}
			$this->Session->setFlash($msg,$type);
		}
		$this->redirect($this->referer(array('controller'=>'users','action'=>'dashboard')));
	}

	/**
	 * Displays database driven pages
	 */
	public function admin_content() {
		$page = false;
		if(!empty($this->request->params['uri'])) {
			$page = $this->Page->findByUri($this->request->params['uri']);
		}

		if(!$page) {
			$this->Session->setFlash('Sorry, the requested page could not be located.', 'messaging/alert-error');
			$this->redirect(array('controller'=>'users', 'action' => 'dashboard'));
		}
		$this->set('page', $page);
	}
}
