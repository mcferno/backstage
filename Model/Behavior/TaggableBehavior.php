<?php

class TaggableBehavior extends ModelBehavior
{
	// the name of the Tag join model.
	public $joinModelName = 'Tagging';

	public function setup(Model $Model, $settings = array())
	{
		$this->joinModel = ClassRegistry::init($this->joinModelName);
	}

	public function afterSave(Model $Model, $created, $options = array())
	{

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
	 * a specific Model.
	 *
	 * @param array $model_conditions Joins the current Model and applies these optional conditions
	 * @return array Order Tagging results with count
	 */
	public function getTagTally(Model $Model, $model_conditions = array())
	{
		$options = array(
			'contain' => array('Tag'),
			'fields' => 'COUNT(*) as count, Tagging.tag_id, Tag.*',
			'group' => 'Tagging.tag_id',
			'conditions' => array(
				'Tagging.model' => $Model->alias
			),
			'order' => 'count DESC'
		);

		if(!empty($model_conditions)) {

			$options['fields'] .= ", {$Model->alias}.*";
			$options['conditions'] = array_merge_recursive($options['conditions'], $model_conditions);
			$options['joins'][] = array(
				'alias' => $Model->alias,
				'type' => 'INNER',
				'table' => $Model->table,
				'conditions' => array(
					"Tagging.foreign_id = {$Model->alias}.id",
					'Tagging.model' => $Model->alias
				)
			);
		}
		return $this->joinModel->find('all', $options);
	}
}