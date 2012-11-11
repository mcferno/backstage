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
}