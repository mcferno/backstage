<?php

class Token extends AppModel
{
	/**
	 * Removes tokens belonging to a related entity, optionally by sub-type
	 *
	 * @param $id
	 * @param string|null $type
	 * @return bool
	 */
	public function removeAllByLookup($id, $type = null)
	{
		$conditions = array(
			'foreign_id' => $id
		);
		if(!empty($type)) {
			$conditions['type'] = $type;
		}

		return $this->deleteAll($conditions, false, false);
	}

	/**
	 * Produces a unique token value.
	 * @return string
	 */
	public static function generateTokenValue()
	{
		return String::uuid() . '-' . String::uuid();
	}

	/**
	 * Write a new token
	 *
	 * @param $foreign_id
	 * @param $expiry
	 * @param null $type
	 * @return array
	 */
	public function addNewToken($foreign_id, $expiry, $type = null)
	{
		return $this->save(array(
			'foreign_id' => $foreign_id,
			'token' => static::generateTokenValue(),
			'expires' => is_null($expiry) ? null : date('Y-m-d H:i:s', strtotime($expiry)),
			'type' => $type
		));
	}
}