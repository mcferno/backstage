<?php

/**
 * Represents the association between a Tag and a Model
 */
class Tagging extends AppModel {
	
	public $belongsTo = array('Tag');

	/**
	 * Accepts a delimited string of tags for a specific model/id and handles
	 * the addition of new tags, removal of undesired tags, and saves all needed
	 * associations.
	 */
	public function saveTags($data) {

		// get existing tag associations
		$taggings = $this->find('all', array(
			'contain' => array('Tag'),
			'conditions' => array(
				'model' => $data['model'],
				'foreign_id' => $data['foreign_id']
			)
		));

		$desired_taggings = explode(',', $data['tags']);
		$existing_taggings = Hash::extract($taggings, '{n}.Tag.name');

		$new_tags = array_diff($desired_taggings, $existing_taggings);
		$remove_tags = array_diff($existing_taggings, $desired_taggings);

		// no work to do, early exit
		if(empty($new_tags) && empty($remove_tags)) {
			return;
		}

		// get any existing system tags
		$tags = $this->Tag->find('all', array(
			'conditions' =>array(
				'name' => array_merge($new_tags, $remove_tags)
			)
		));

		$remove_tag_ids = array();
		$add_tag_ids = array(); 

		// assign each existing tag for association, or disassociation
		foreach($tags as $tag) {
			// queue for disassociation
			if(in_array($tag['Tag']['name'], $remove_tags)) {
				$remove_tag_ids[] = $tag['Tag']['id'];

			// add existing tag association
			} else {
				$add_tag_ids[] = array(
					'tag_id' => $tag['Tag']['id'],
					'model' => $data['model'],
					'foreign_id' => $data['foreign_id'],
					'user_id' => $data['user_id']
				);
				$new_tags = array_diff($new_tags, array($tag['Tag']['name']));
			}
		}

		// process remaining new tags
		foreach ($new_tags as $tag) {
			$this->Tag->create();
			$this->Tag->save(array(
				'user_id' => $data['user_id'],
				'name' => $tag
			));
			$add_tag_ids[] = array(
				'tag_id' => $this->Tag->id,
				'model' => $data['model'],
				'foreign_id' => $data['foreign_id'],
				'user_id' => $data['user_id']
			);
		}

		// disassociate tags
		if(!empty($remove_tag_ids)) {
			$this->deleteAll(array(
				'foreign_id' => $data['foreign_id'],
				'model' => $data['model'],
				'tag_id' => $remove_tag_ids
			));
		}

		// associate tags
		if(!empty($add_tag_ids)) {
			$this->saveMany($add_tag_ids);
		}
	}
}