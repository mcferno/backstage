<?php
App::uses('AppModel', 'Model');

class Tag extends AppModel {

	public $displayField = 'name';
	public $order = array('Tag.name' => 'ASC');
	public $belongsTo = array('User');

	public function getListForModel($model) {
		return $this->find('list', array(
			'joins' => array(
				array(
					'table' => 'taggings',
					'alias' => 'Tagging',
					'type' => 'INNER',
					'conditions' => array(
						'Tag.id = Tagging.tag_id',
						'Tagging.model' => $model
					)
				)
			)
		));
	}
}