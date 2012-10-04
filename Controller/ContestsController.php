<?php
class ContestsController extends AppController {

	public $paginate = array(
		'Contest' => array(
			'contain' => array('User', 'Asset'),
			'order' => 'Contest.created DESC'		
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
				$this->Session->setFlash('Your caption battle has started!','messaging/alert-success');
				$this->redirect(array('action' => 'view', $this->Contest->id));
			} else {
				$this->Session->setFlash('There was an error with your caption battle set up. Please try again.','messaging/alert-error');
			}
		}
	}

}