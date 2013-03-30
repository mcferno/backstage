<?php

class TagsController extends AppController {

	public $uses = array('Tag', 'Tagging');

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