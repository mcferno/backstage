<?php
class ContestsController extends AppController {

	public $paginate = array(
		'Contest' => array(
			'contain' => array('User', 'Asset'),
			'order' => 'Contest.created DESC'		
		),
		'Asset' => array(
			'contain' => array('User'),
			'order' => 'Asset.created DESC'
		)
	);

	public $uses = array('Contest', 'Asset');

	/**
	 * Shows any active contests, and the archive of past contests.
	 */
	public function admin_index() {
		$this->set('activeContests', $this->Contest->getActiveContests());
		$this->set('contests', $this->paginate());
	}

	/**
	 * Initialize a new contest
	 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->Contest->create();
			if ($this->Contest->save($this->request->data)) {			
				$this->Session->setFlash('Your caption battle has started!', 'messaging/alert-success');
				$this->redirect(array('action' => 'view', $this->Contest->id));
			} else {
				$this->Session->setFlash('There was an error with your caption battle set up. Please try again.','messaging/alert-error');
			}
		}
	}

	/**
	 * View an individual contest, and all of its entries via pagination
	 */
	public function admin_view($id = null) {

		if(empty($id) || !$this->Contest->exists($id)) {
			$this->Session->setFlash('Sorry, that contest doesn\'t appear to exist', 'messaging/alert-error');
			$this->redirect(array('action' => 'index'));
		}

		$contest = $this->Contest->find('first', array(
			'contain' => array('Asset', 'User', 'Winner'),
			'conditions' => array(
				'Contest.id' => $id
			)
		));

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
	}

	/**
	 * Declares the winning caption (Asset) for a specific Contest. Only the
	 * contest owner can declare the winner. The creator can be the winner.
	 *
	 * @param {UUID} $contest_id Contest to declare the winner of
	 * @param {UUID} $asset_id Asset chosen as winner
	 */
	public function admin_set_winner($contest_id = null, $asset_id = null) {

		// sanity check
		if(empty($contest_id) || empty($asset_id) || !$this->Contest->exists($contest_id) || !$this->Asset->exists($asset_id)) {
			$this->Session->setFlash('Malformed URL.', 'messaging/alert-error');
			$this->redirect($this->referer(array('action' => 'admin_index')));
		}

		$asset = $this->Asset->find('first', array(
			'contain' => 'User',
			'conditions' => array(
				'Asset.id' => $asset_id
			)
		));

		// enforce ownership of the contest
		if(!$this->Contest->isOwner($contest_id, $asset['User']['id'])) {
			$this->Session->setFlash('Sorry, only the Caption Battle creator can declare the winner.', 'messaging/alert-error');
			$this->redirect($this->referer(array('action' => 'admin_view', $contest_id)));
		}

		// enforce that a Contest not be closed too quickly
		if($this->Contest->isRecent($contest_id)) {
			$this->Session->setFlash('Sorry, the Caption Battle is too new, please allow a day or more for everyone to have a chance to submit entries.', 'messaging/alert-error');
			$this->redirect($this->referer(array('action' => 'admin_view', $contest_id)));
		}

		// set the winner
		if($this->Contest->setWinningAsset($contest_id, $asset_id)) {
			$this->Session->setFlash('The Caption Battle has ended, the winner is now set.', 'messaging/alert-success');
			$this->redirect(array('action' => 'admin_view', $contest_id));
		}
	}

}