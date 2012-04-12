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
		
		if ($this->request->is('post') || $this->request->is('put')) {
			$data = $this->request->data;
			$data['Message']['user_id'] = $this->Session->read('Auth.User.id');
			$this->Message->save($data,false);
		}
		
		$this->set('response',array('success'=>true));
		$this->set('_serialize', array('response','body'));
	}
	
	public function admin_getLatest() {
		$messages = $this->Message->find('all',array(
			'contain'=>'User',
			'conditions'=>array(
				'Message.created >='=>date('Y-m-d H:i:s',strtotime('now -1 day'))
			),
			'order'=>'Message.created ASC'
		));
//		foreach($messages as &$message) {
//			$message['Message']['timestamp'] = strtotime($message['Message']['created']);
//		}
		$this->set('messages',$messages);
		$this->set('_serialize', array('messages'));
	}
}