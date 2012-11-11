<?php
App::uses('AppController', 'Controller');

class LinksController extends AppController {

	public function admin_index() {
		$this->paginate = array(
			'contain' => array('User', 'Tag')
		);
		$this->set('links', $this->paginate());
	}

	public function admin_view($id = null) {
		$this->Link->id = $id;
		if (!$this->Link->exists()) {
			throw new NotFoundException(__('Invalid link'));
		}
		$link = $this->Link->find('first', array(
			'contain' => array('User', 'Tag'),
			'conditions' => array(
				'Link.id' => $id
			)
		));
		$this->set('link', $link);
	}

	public function admin_add() {
		if ($this->request->is('post')) {
			$this->Link->create();
			$this->Link->set('user_id', $this->Auth->user('id'));

			if ($this->Link->save($this->request->data)) {
				$this->Session->setFlash('Your new link has been added!', 'messaging/alert-success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Your new link could not be saved. Please, try again.', 'messaging/alert-error');
			}
		}
		
		$this->set('tags', array_values($this->Link->Tag->find('list')));
	}

	public function admin_edit($id = null) {
		$this->Link->id = $id;
		if (!$this->Link->exists()) {
			throw new NotFoundException(__('Invalid link'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Link->save($this->request->data)) {
				$this->Session->setFlash('Your link has been updated!', 'messaging/alert-success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Your link could not be updated. Please, try again.', 'messaging/alert-error');
			}
		} else {
			
			$this->request->data = $this->Link->find('first', array(
				'contain' => 'Tag',
				'conditions' => array(
					'Link.id' => $id
				)
			));
			
			// compile existing tags
			if(!empty($this->request->data['Tag'])) {
				$this->request->data['Tagging']['tags'] = implode(Hash::extract($this->request->data['Tag'], '{n}.name'), ',');
			}
		}

		$this->set('tags', array_values($this->Link->Tag->find('list')));
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
			$this->Session->setFlash('Your link has been removed!', 'messaging/alert-success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Your link could not be deleted. Please, try again.', 'messaging/alert-error');
		$this->redirect(array('action' => 'index'));
	}
}
