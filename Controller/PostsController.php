<?php
App::uses('AppController', 'Controller');

class PostsController extends AppController {

	public $paginate = array(
		'order' => 'date DESC',
		'limit' => 10
	);
	
	public $cacheAction = array(
		'view' => '+1 hour'
	);
		
	/**
	 * Isolating the homepage for finer-grain cache control 
	 */
	public function home() {
		$this->cacheAction = '+10 minutes';
		$this->setAction('index');
	}
	
	/**
	 * Primary index of all aggregated posts.
	 */
	public function index() {
		$this->set('posts',$this->paginate());
		
		// pull Twitter accounts to obtain their most recent profile image
		$accounts = ClassRegistry::init('Account')->find('all',array('fields'=>array('handle','profile_image')));
		$this->set('accounts',Set::combine($accounts,'/Account/handle','/Account/profile_image'));
	}
	
	/**
	 * Specific post view page.
	 */
	public function view() {
		
		// no UUID provided
		if(empty($this->request->params['id'])) {
			$this->redirect($this->referer('/'));
		}
		
		$post = $this->Post->find('first',array(
			'conditions'=>array(
				'id'=>$this->request->params['id'])
			)
		);
		
		// post does not exist
		if(empty($post)) {
			$this->redirect($this->referer('/'));
		}
		
		$this->set('post',$post);
		
		// pull Twitter accounts to obtain their most recent profile image
		$accounts = ClassRegistry::init('Account')->find('all',array('fields'=>array('handle','profile_image')));
		$this->set('accounts',Set::combine($accounts,'/Account/handle','/Account/profile_image'));
	}
	
	public function admin_index() {
		$this->Post->recursive = 0;
		$this->paginate['limit'] = 20;
		$this->set('posts', $this->paginate());
		
		$types = $this->Post->find('all',array('fields'=>'model','group'=>'model'));
		$this->set('types',Set::extract('/Post/model',$types));
	}
}
