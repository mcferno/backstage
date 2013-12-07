<?php

class AlbumsController extends AppController {

	public function admin_save($id = null) {

		if ($this->request->is('post') || $this->request->is('put')) {
			if(!empty($this->request->data['Album']['id'])) {
				$status = 'updated';
			} else {
				$status = 'created';
				$this->request->data['Album']['user_id'] = $this->Session->read('Auth.User.id');
				if(trim($this->request->data['Album']['title']) == '') {
					$this->request->data['Album']['title'] = 'Untitled Album';
				}
			}
			if ($this->Album->save($this->request->data)) {
				$this->Session->setFlash('The Album has been ' . $status, 'messaging/alert-success');
			} else {
				$this->Session->setFlash("The album could not be {$status}. Please, try again.", 'messaging/alert-error');
			}
			$this->redirect($this->referer(array('action' => 'index')));
		}
	}

}