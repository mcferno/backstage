<?php
App::uses('AppController', 'Controller');

class PostsController extends AppController
{
	public $paginate = array(
		'order' => 'date DESC',
		'limit' => 10
	);

	public function admin_index()
	{
		$this->Post->recursive = 0;
		$this->paginate['limit'] = 20;
		$this->set('posts', $this->paginate());

		$types = $this->Post->find('all', array('fields' => 'model', 'group' => 'model'));
		$this->set('types', Set::extract('/Post/model', $types));
	}
}
