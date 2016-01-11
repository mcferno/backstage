<?php

/**
 * Adds utility functions to any Models which have a User association implying
 * content ownership. Example: User owns his/her Profile
 */
class OwnableBehavior extends ModelBehavior {

	/**
	 * Determines if the current Model instance is owned by the provided UserID
	 *
	 * @param string $user_id User ID to verify against current Model instance
	 * @param string $primary_key Replacement ID to verify ownership for, ignoring Model->id
	 * @return boolean
	 */
	public function isOwner(&$Model, $user_id, $primary_key = false) {
		if(empty($Model->id) && $primary_key === false) {
			return false;
		}

		$key = ($primary_key === false) ? $Model->id : $primary_key;

		return $Model->hasAny(array(
			"{$Model->alias}.{$Model->primaryKey}" => $key,
			"{$Model->alias}.user_id" => $user_id
		));
	}

}