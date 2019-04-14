<?php

class ContestsController extends AppController
{
	public $paginate = array(
		'Contest' => array(
			'conditions' => array(
				'winning_asset_id IS NOT NULL'
			),
			'contain' => array('User', 'Asset'),
			'order' => 'Contest.created DESC'
		),
		'Asset' => array(
			'contain' => array('User'),
			'order' => 'Asset.created DESC'
		)
	);

	public $uses = array('Contest', 'Asset');

	public function adminBeforeRender()
	{
		parent::adminBeforeRender();
		$this->set('title', 'Caption Battles');
	}

	/**
	 * Shows any active contests, and the archive of past contests.
	 */
	public function admin_index()
	{
		$this->set('activeContests', $this->Contest->getActiveContests());
		$this->paginate['Contest']['limit'] = 100;
		$this->set('contests', $this->paginate());
	}

	/**
	 * Initialize a new contest
	 */
	public function admin_add()
	{
		if($this->request->is('post')) {
			$this->Contest->create();
			if($this->Contest->save($this->request->data)) {
				$this->Session->setFlash('Your caption battle has started!', 'messaging/alert-success');

				// announce contest if FB integration exists
				if($this->Session->check('Auth.User.fb_target')) {
					$this->redirect(array('action' => 'announce', $this->Contest->id));

				} else {
					$this->redirect(array('action' => 'view', $this->Contest->id));
				}

			} else {
				$this->Session->setFlash('There was an error with your caption battle set up. Please try again.', 'messaging/alert-error');
			}
		}
	}

	/**
	 * Announces a caption battle over Facebook
	 */
	public function admin_announce($id = null)
	{
		if(!$id || !$this->Session->check('Auth.User.fb_target')) {
			$this->redirect(array('action' => 'index'));
		}

		$contest = $this->Contest->findById($id);
		$contest_route = array('action' => 'view', $id);

		// contest not found, or user is not the contest owner
		if(!$contest || !Access::isOwner($contest['Contest']['user_id'])) {
			$this->Session->setFlash('Caption battle could not be found. Please try again.', 'messaging/alert-error');
			$this->redirect($contest_route);
		}

		if($this->User->hasFacebookAccess()) {
			$asset_path = $this->Asset->getPath($contest['Contest']['asset_id'], 200);

			$fbPost = array(
				'link' => Router::url($contest_route, true),
				'picture' => Router::url('/', true) . IMAGES_URL . $asset_path,
				'name' => $this->Contest->fbStrings['new_title'],
				'caption' => CakeText::insert($this->Contest->fbStrings['new_caption'], array('user' => $this->Auth->user('username'))),
				'description' => CakeText::insert($this->Contest->fbStrings['new_desc'], array('site_name' => Configure::read('Site.name')))
			);

			// attach optional message
			if(!empty($contest['Contest']['message']) && trim($contest['Contest']['message']) != '') {
				$fbPost['message'] = $contest['Contest']['message'];
			}

			$result = $this->User->facebookApiCall('/' . $this->Session->read('Auth.User.fb_target') . '/feed', 'POST', $fbPost);

			// post was successful, record the id for reference
			if(!empty($result['id'])) {
				$this->Contest->id = $id;
				$this->Contest->saveField('fb_id', $result['id']);

				$this->Session->setFlash('Your caption battle has been announced on Facebook!', 'messaging/alert-success');
				$this->redirect($contest_route);
			}

			$this->Session->setFlash('An error occurred while attempting to post to Facebook.', 'messaging/alert-error');
			$this->redirect($contest_route);

		// send the user away to authenticate
		} else {
			$redirect_url = $this->User->getFacebookLoginUrl(Router::url(array('controller' => 'contests', 'action' => 'admin_announce', $id), true));
			$this->redirect($redirect_url);
		}
	}

	/**
	 * View an individual contest, and all of its entries via pagination.
	 * When a contest has ended (winner chosen), page:1 corresponds to the winning entry
	 */
	public function admin_view($id = null)
	{
		if(empty($id) || !$this->Contest->exists($id)) {
			$this->Session->setFlash('Sorry, that contest doesn’t appear to exist', 'messaging/alert-error');
			$this->redirect(array('action' => 'index'));
		}

		$contest = $this->Contest->find('first', array(
			'contain' => array('Asset', 'User', 'Winner'),
			'conditions' => array(
				'Contest.id' => $id
			)
		));

		if(!empty($contest['Winner'])) {
			$this->Asset->addMetaData($contest['Winner']);
		}

		$this->set('contest', $contest);

		// fetch all entries for the landing page
		if(isset($this->request->params['named']['page'])) {
			$this->paginate['Asset']['limit'] = 1;
		} else {
			$this->paginate['Asset']['limit'] = 100;
		}

		// set the winning entry to the first position
		if(!empty($contest['Contest']['winning_asset_id'])) {
			$this->paginate['Asset']['order'] = "Asset.id = \"{$contest['Contest']['winning_asset_id']}\" DESC, Asset.created DESC";
		}

		// attach all assets associated to this contest
		$this->paginate['Asset']['joins'][] = array(
			'table' => 'assets_contests',
			'alias' => 'AssetsContest',
			'type' => 'INNER',
			'conditions' => array(
				'Asset.id = AssetsContest.asset_id',
				'AssetsContest.contest_id' => $id
			)
		);

		$this->set('assets', $this->paginate('Asset'));
		$this->set('page_limits', false);
	}

	/**
	 * Declares the winning caption (Asset) for a specific Contest. Only the
	 * contest owner can declare the winner. The creator can be the winner.
	 *
	 * @param string $contest_id Contest to declare the winner of
	 * @param string $asset_id Asset chosen as winner
	 */
	public function admin_set_winner($contest_id = null, $asset_id = null)
	{
		// sanity check
		if(empty($contest_id) || empty($asset_id) || !$this->Contest->exists($contest_id) || !$this->Asset->exists($asset_id)) {
			$this->Session->setFlash('Malformed URL.', 'messaging/alert-error');
			$this->redirect($this->referer(array('action' => 'index')));
		}

		$asset = $this->Asset->find('first', array(
			'contain' => 'User',
			'conditions' => array(
				'Asset.id' => $asset_id
			)
		));

		// enforce ownership of the contest
		if(!$this->Contest->isOwner($contest_id, $this->Auth->user('id'))) {
			$this->Session->setFlash('Sorry, only the Caption Battle creator can declare the winner.', 'messaging/alert-error');
			$this->redirect($this->referer(array('action' => 'view', $contest_id)));
		}

		// enforce that a Contest not be closed too quickly
		if($this->Contest->isRecent($contest_id, 12 * HOUR)) {
			$this->Session->setFlash('Sorry, the Caption Battle is too new, please allow 12 hrs or more for everyone to have a chance to submit entries.', 'messaging/alert-error');
			$this->redirect($this->referer(array('action' => 'view', $contest_id)));
		}

		// set the winner
		if($this->Contest->setWinningAsset($contest_id, $asset_id)) {
			$this->Session->setFlash('The Caption Battle has ended, the winner is now set.', 'messaging/alert-success');

			// announce winner if FB integration exists
			if($this->Session->check('Auth.User.fb_target')) {
				$this->redirect(array('action' => 'announce_winner', $contest_id));
			} else {
				$this->redirect(array('action' => 'view', $contest_id, 'page' => 1));
			}
		}
	}

	/**
	 * Announces a contest winner on Facebook
	 */
	public function admin_announce_winner($id = null)
	{
		if(!$id || !$this->Session->check('Auth.User.fb_target')) {
			$this->redirect(array('action' => 'index'));
		}

		$contest = $this->Contest->find('first', array(
			'contain' => array('Winner' => 'User'),
			'conditions' => array(
				'Contest.id' => $id
			)
		));

		$contest_route = array('action' => 'view', $id);
		$winner_route = array('action' => 'view', $id, 'page' => 1);

		// contest not found, or user is not the contest owner
		if(!$contest || !Access::isOwner($contest['Contest']['user_id'])) {
			$this->Session->setFlash('Caption battle could not be found. Please try again.', 'messaging/alert-error');
			$this->redirect($contest_route);
		}

		$fbSDK = $this->User->getFacebookObject();

		// verify active FB user session
		if($fbSDK->getUser()) {

			$asset_path = $this->Asset->getPath($contest['Contest']['winning_asset_id'], 200);

			// creator chose himself as winner
			if(Access::isOwner($contest['Winner']['User']['id'])) {
				$winner = $this->Auth->user('username') . ' has chosen himself as the winner.';
			} else {
				$winner = $this->Auth->user('username') . " has chosen {$contest['Winner']['User']['username']} as the winner.";
			}

			$fbPost = array(
				'link' => Router::url($winner_route, true),
				'picture' => Router::url('/', true) . IMAGES_URL . $asset_path,
				'name' => $this->Contest->fbStrings['winner_title'],
				'caption' => $winner,
				'description' => $this->Contest->fbStrings['winner_desc']
			);

			$endpoint = '/' . $this->Session->read('Auth.User.fb_target') . '/feed';
			try {
				$res = $fbSDK->api($endpoint, 'POST', $fbPost);

				// post was successful, record the id for reference
				if(!empty($res['id'])) {
					$this->Session->setFlash('The caption battle winner has been announced on Facebook!', 'messaging/alert-success');
					$this->redirect($winner_route);
				}
			} catch(FacebookApiException $e) {
				$this->log("FB API contest winnner Exception for {$endpoint}");
				$this->log($e->getType());
				$this->log($e->getMessage());
			}

			$this->Session->setFlash('An error occurred while attempting to post to Facebook.', 'messaging/alert-error');
			$this->redirect($contest_route);
		}

		// send the user away to authenticate
		$login_params = array(
			'scope' => $this->User->getFacebookPermissions(),
			'redirect_uri' => Router::url(array('action' => 'announce_winner', $id), true)
		);

		$this->redirect($fbSDK->getLoginUrl($login_params));
	}

}