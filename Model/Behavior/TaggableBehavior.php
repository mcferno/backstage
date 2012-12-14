<?php

class TaggableBehavior extends ModelBehavior {

	// the name of the Tag join model.
	public $joinModelName = 'Tagging';

	public function setup(Model $Model, array $settings = array()) {
		$this->joinModel = ClassRegistry::init($this->joinModelName);
	}

	public function afterSave(Model $Model, $created) {

		// detect string tags and convert to proper relationships
		if(!empty($Model->data[$this->joinModelName]['tags'])) {
			$data = $Model->data[$this->joinModelName];

			if($created) {
				$data['foreign_id'] = $Model->id;
			}
			
			$this->joinModel->saveTags($data);
		}
	}

	/**
	 * Retrieves a count of all tags, and how many times they've been used for
	 * a specific Model. Optionally filtered to only one User's content.
	 */
	public function getTagTally(Model $Model, $by_user = false) {
		$options = array(
			'contain' => array('Tag'),
			'fields' => 'COUNT(*) as count, Tagging.tag_id, Tag.*',
			'group' => 'Tagging.tag_id',
			'conditions' => array(
				'Tagging.model' => $Model->alias
			),
			'order' => 'count DESC'
		);

		if($by_user) {

			$options['fields'] .= ", {$Model->alias}.*";
			$options['conditions']["{$Model->alias}.user_id"] = $by_user;
			$options['joins'][] = array(
				'alias' => $Model->alias,
				'type' => 'INNER',
				'table' => $Model->table,
				'conditions'=> array(
					"Tagging.foreign_id = {$Model->alias}.id",
					'Tagging.model' => $Model->alias
				)
			);
		}
		return $this->joinModel->find('all', $options);
	}
}