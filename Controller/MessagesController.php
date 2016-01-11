<?php
/**
 * Messaging Module
 */
class MessagesController extends AppController {
	public $uses = array('Message');
	public $paginate = array(
		'contain' => 'User',
		'order' => 'Message.created DESC'
	);

	public $restrictedRoutes = array('admin_index', 'admin_delete');

	public function adminBeforeRender() {
		parent::adminBeforeRender();
		$this->set('title', 'Message Log');
	}

	/**
	 * Saves a new message.
	 */
	public function admin_add() {

		$response = array();

		if ($this->request->is('post') || $this->request->is('put')) {
			$data = $this->request->data;
			$data['Message']['user_id'] = $this->Session->read('Auth.User.id');
			$this->Message->save($data,false);

			$newMessage = $this->Message->find('first', array(
				'contain' => 'User',
				'conditions' => array(
					'Message.id' => $this->Message->id
				)
			));
			if(!empty($newMessage)) {
				$newMessage['Message']['timestamp'] = strtotime($newMessage['Message']['created']);
				$response['messages'][] = $newMessage;
			}
		}

		if($this->request->is('ajax')) {
			$base_data = $this->_getHeartbeatData();
			$response = array_merge_recursive($base_data, $response);
		}
		$response['success'] = true;

		$this->set($response);
		$this->set('_serialize', array_keys($response));
	}

	public function admin_index() {
		if(!empty($this->request->params['named']['model'])) {
			$this->paginate['conditions']['Message.model'] = $this->request->params['named']['model'];
		}
		if(!empty($this->request->params['named']['user'])) {
			$this->paginate['conditions']['Message.user_id'] = $this->request->params['named']['user'];
		}
		if(!empty($this->request->params['named']['text'])) {
			$this->paginate['conditions']['Message.text LIKE'] = "%{$this->request->params['named']['text']}%";
		}
		$this->set('messages', $this->paginate());
	}

	public function admin_delete($id = null) {
		if (!$this->request->is('post') || !Access::hasRole('Admin')) {
			throw new MethodNotAllowedException();
		}
		$this->Message->id = $id;
		if (!$this->Message->exists()) {
			throw new NotFoundException(__('Invalid message'));
		}

		if($this->Message->delete()) {
			$this->Session->setFlash('The message has been removed!', 'messaging/alert-success');
			$this->redirect($this->referer(array('action' => 'index')));
		}
		$this->Session->setFlash('The message could not be deleted. Please, try again.', 'messaging/alert-error');
		$this->redirect($this->referer(array('action' => 'index')));
	}
}