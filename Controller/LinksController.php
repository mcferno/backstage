<?php
App::uses('AppController', 'Controller');

class LinksController extends AppController {

	public function admin_index() {
		$this->Link->recursive = 0;
		$this->set('links', $this->paginate());
	}

	public function admin_view($id = null) {
		$this->Link->id = $id;
		if (!$this->Link->exists()) {
			throw new NotFoundException(__('Invalid link'));
		}
		$this->set('link', $this->Link->read(null, $id));
	}

	public function admin_add() {
		if ($this->request->is('post')) {
			$this->Link->create();
			if ($this->Link->save($this->request->data)) {
				$this->Session->setFlash('Your new linked has been added!', 'messaging/alert-success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The link could not be saved. Please, try again.'));
			}
		}
		$users = $this->Link->User->find('list');
		$this->set(compact('users'));
	}

	public function admin_edit($id = null) {
		$this->Link->id = $id;
		if (!$this->Link->exists()) {
			throw new NotFoundException(__('Invalid link'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Link->save($this->request->data)) {
				$this->Session->setFlash(__('The link has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The link could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Link->read(null, $id);
		}
		$users = $this->Link->User->find('list');
		$this->set(compact('users'));
	}

	public function admin_delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Link->id = $id;
		if (!$this->Link->exists()) {
			throw new NotFoundException(__('Invalid link'));
		}
		if ($this->Link->delete()) {
			$this->Session->setFlash(__('Link deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Link was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
