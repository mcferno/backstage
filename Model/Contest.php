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

	/**
	 * Determines whether the Contest is owned by a specific user.
	 * 
	 * @param {UUID} $contest_id Contest to determine ownership
	 * @param {UUID} $user_id User in question
	 * @return {Boolean} Whether or not the User provided is the Contest owner
	 */
	public function isOwner($contest_id, $user_id) {
		if(empty($contest_id) || empty($user_id)) {
			return false;
		}

		return $this->hasAny(array(
			'id' => $contest_id,
			'user_id' => $user_id
		));
	}

	/**
	 * Determines if a Contest is newer than a specific time period
	 * 
	 * @param {UUID} $contest_id Contest to determine freshness
	 * @param {Integer} $duration Length in seconds by which the Contest must be older
	 * @return {Boolean} Whether or not the contest is considered recent
	 */
	public function isRecent($contest_id, $duration = DAY) {
		$this->id = $contest_id;
		$created = $this->field('created');

		if($created !== false) {
			return time() - strtotime($created) <= $duration;
		}

		return false;
	}

	/**
	 * Sets the winning Asset for a specific Contest
	 * 
	 * @param {UUID} $contest_id Contest to set the winner for
	 * @param {UUID} $asset_id Asset to set as the winner
	 * @param {Boolean} $force Whether or not to override an existing winner
	 * @return {Boolean} Save status
	 */
	public function setWinningAsset($contest_id, $asset_id, $force = false) {
		
		// verify that this Contest has no existing winner
		if(!$force && !$this->hasAny(array('id' => $contest_id, 'winning_asset_id IS NULL'))) {
			return false;
		}

		$this->create();
		$this->id = $contest_id;
		return $this->saveField('winning_asset_id', $asset_id);
	}
}
