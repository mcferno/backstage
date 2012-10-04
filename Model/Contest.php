<?php

/**
 * Contest Model
 *
 * A caption contest between users, all using the same base image in the Meme
 * Generation tool. One user initializes the contest, and all users can 
 * participate by submitting their own entries.
 */
class Contest extends AppModel {

	public $displayField = 'message';

	public $belongsTo = array(
		
		// the creator of the contest
		'User' ,
		
		// the base image for this contest
		'Asset',
		
		// the winning asset
		'Winner' => array(
			'className' => 'Asset',
			'foreignKey' => 'winning_asset_id'
		)
	);

	public $hasAndBelongsToMany = array(

		// image entries into the contest
		'Entry' => array(
			'className' => 'Asset'
		)
	);

	public function getActiveContests() {

		return $this->find('all' , array(
			'contain' => array('User', 'Asset'),
			'conditions' => array(
				"{$this->alias}.winning_asset_id IS NULL"
			)
		));

	}

}