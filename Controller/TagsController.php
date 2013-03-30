<?php

class TagsController extends AppController {

	public $uses = array('Tag', 'Tagging');
	public $paginate = array(
		'Tag' => array(
			'contain' => 'User',
			'order' => 'Tag.count DESC',
			'joins' => array(
				array(
					'alias' => 'Tagging',
					'type' => 'LEFT',
					'table' => 'taggings',
					'conditions'=> array(
						"Tagging.tag_id = Tag.id",
					)
				)
			),
			'group' => 'Tag.id'
		)
	);
	public $scaffold = 'admin';

	public $restrictedRoutes = array('admin_index');

	public function beforeScaffold($method) {
		if(!Access::hasRole('Admin')) {
			$this->redirect(array('controller'=>'users', 'action' => 'dashboard'));
		}
		return parent::beforeScaffold($method);
	}

	/**
	 * Admin overview index
	 */
	public function admin_index() {
		if(!empty($this->request->params['named']['user'])) {
			$this->paginate['Tag']['conditions']['Tag.user_id'] = $this->request->params['named']['user'];
		}
		if(!empty($this->request->params['named']['name'])) {
			$this->paginate['Tag']['conditions']['Tag.name LIKE'] = "%{$this->request->params['named']['name']}%";
		}

		$tags = $this->paginate('Tag');
		$this->set('tags', $tags);
	}

	/**
	 * AJAX-driven tag updates
	 */
	public function admin_update() {
		if($this->request->is('ajax') && ($this->request->is('post') || $this->request->is('put'))) {

			$data = array(
				'foreign_id' => $this->request->data['Tag']['id'],
				'tags' => $this->request->data['Tag']['tags'],
				'model' => $this->request->data['Tag']['model'],
				'user_id' => $this->Auth->user('id')
			);

			$this->Tagging->saveTags($data);
			$this->set('_serialize', true);
		}
	}

}