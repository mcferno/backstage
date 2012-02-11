<?php
App::uses('AppController', 'Controller');

class PostsController extends AppController {

	public $paginate = array(
		'order' => 'date DESC',
		'limit' => 10
	);
		
	public $helpers = array('Paginator');
	
	public function index() {
		$this->set('posts',$this->paginate());
		
		// pull Twitter accounts to obtain their most recent profile image
		$accounts = ClassRegistry::init('Account')->find('all',array('fields'=>array('handle','profile_image')));
		$this->set('accounts',Set::combine($accounts,'/Account/handle','/Account/profile_image'));
	}
}
