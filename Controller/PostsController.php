<?php
App::uses('AppController', 'Controller');

class PostsController extends AppController {

	public $paginate = array(
		'order' => 'date DESC',
		'limit' => 10
	);
		
	public $helpers = array('Paginator');
	
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
	 * URL triggered data refresh. Rate limited to avoid abuse of taxing db
	 * operations.
	 */
	public function refresh() {
		ClassRegistry::init('Tumblr')->lazyCron();
		ClassRegistry::init('Twitter')->lazyCron();
		$this->redirect('/');
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
}
