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

		$this->set('contest', $this->Contest->find('first', array(
			'contain' => array('Asset', 'User', 'Winner'),
			'conditions' => array(
				'Contest.id' => $id
			)
		)));

		// fetch all entries for the landing page
		if(isset($this->request->params['named']['page'])) {
			$this->paginate['Asset']['limit'] = 1;
		} else {
			$this->paginate['Asset']['limit'] = 100;
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

}