<?php

class MessagesController extends AppController {
	public $uses = array('Message');
	
	public function adminBeforeFilter() {		
		if($this->request->is('ajax')) {
			$this->disableCache(); // expire cache immediately
			$this->RequestHandler->renderAs($this, 'json');
			$this->Security->validatePost = false;
			$this->Security->csrfCheck = false;
		}
		parent::adminBeforeFilter();	
	}
	
	public function admin_add() {
		
		$response = array();
		
		if ($this->request->is('post') || $this->request->is('put')) {
			$data = $this->request->data;
			$data['Message']['user_id'] = $this->Session->read('Auth.User.id');
			$this->Message->save($data,false);
			
			$newMessage = $this->Message->find('first',array(
				'contain'=>'User',
				'conditions'=>array(
					'Message.id'=>$this->Message->id
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
}