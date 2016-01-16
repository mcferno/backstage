<?php

class TagsController extends AppController
{
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
					'conditions' => array(
						"Tagging.tag_id = Tag.id",
					)
				)
			),
			'group' => 'Tag.id'
		)
	);
	public $scaffold = 'admin';

	public $restrictedRoutes = array('admin_index');

	public function beforeScaffold($method)
	{
		if(!Access::hasRole('Admin')) {
			$this->redirect(array('controller' => 'users', 'action' => 'dashboard'));
		}
		return parent::beforeScaffold($method);
	}

	public function adminBeforeRender()
	{
		parent::adminBeforeRender();
		$this->set('title', 'Tags');
	}

	/**
	 * Admin overview index
	 */
	public function admin_index()
	{
		$this->Tag->virtualFields['count'] = 'COUNT(*)';

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
	 * AJAX-driven tag updates. Requires all tags to be sent for a single item.
	 */
	public function admin_update()
	{
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

	/**
	 * AJAX-driven tagging additions. Allows multiple tags (existing and new) to
	 * be associated to a set of images.
	 */
	public function admin_add_tags()
	{
		if($this->request->is('ajax') && ($this->request->is('post') || $this->request->is('put'))) {

			$user_id = $this->Auth->user('id');
			$model = $this->request->data['model'];

			foreach($this->request->data['tags'] as $tag) {

				// UUIDs for existing tags
				if(strlen($tag) === 36 && substr_count($tag, '-') === 4) {
					$this->Tagging->addTagToMany($tag, $user_id, $model, $this->request->data['tagged']);

				// string-based Tag to save first.
				} else {

					$this->Tag->save(array(
						'user_id' => $user_id,
						'name' => $tag
					));

					$this->Tagging->addTagToMany($this->Tag->id, $user_id, $model, $this->request->data['tagged']);
				}
			}
		}
		exit();
	}

	/**
	 * Obtains the current full set of Tags in the system
	 */
	public function admin_list()
	{
		if($this->request->is('ajax')) {

			// pull tags in a Select2 compatible format
			$tags = $this->Tag->find('all', array('fields' => 'id, name AS text'));
			$this->set('tags', Hash::extract($tags, '{n}.Tag'));
			$this->set('_serialize', 'tags');
		}
	}

}