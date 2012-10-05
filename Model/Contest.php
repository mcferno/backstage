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
		'User',
		
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

	/**
	 * Obtains a single active contest
	 *
	 * @param {UUID} Primary key of the desired contest
	 * @return {Asset} Active contest matching $id, or false
	 */
	public function getActiveContest($id) {

		return $this->find('first', array(
			'contain' => array('User', 'Asset'),
			'conditions' => array(
				"{$this->alias}.winning_asset_id IS NULL",
				"{$this->alias}.id" => $id
			)
		));

	}

	/**
	 * Obtains the set of all active contests
	 * 
	 * @return {Asset[]} All active contests
	 */
	public function getActiveContests() {

		return $this->find('all' , array(
			'contain' => array('User', 'Asset'),
			'conditions' => array(
				"{$this->alias}.winning_asset_id IS NULL"
			),
			'order' => "{$this->alias}.created DESC"
		));

	}

}