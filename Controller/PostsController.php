<?php
App::uses('AppController', 'Controller');

class PostsController extends AppController {

	public $paginate = array(
		'order' => 'date DESC',
		'limit' => 10
	);
	
	public $helpers = array('Paginator');
	
	public function index() {
		print_r($this->request);
		$this->set('posts',$this->paginate());
	}
}
