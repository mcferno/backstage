<?php

class Album extends AppModel
{
	public $belongsTo = array(
		'Cover' => array(
			'className' => 'Asset',
			'foreignKey' => 'cover_id'
		),
		'User'
	);

	public $hasMany = array(
		'Asset',

		// asset counts per album
		'AssetCount' => array(
			'className' => 'Asset',
			'foreignKey' => false,
			'finderQuery' => '
				SELECT `album_id`, COUNT(`album_id`) as `count`
				FROM `assets` as `AssetCount`
				WHERE `AssetCount`.album_id IN ({$__cakeID__$})
				GROUP BY `album_id`
			'
		),

		// placeholder cover images
		'DefaultCover' => array(
			'className' => 'Asset',
			'foreignKey' => 'album_id',
			'order' => 'created DESC',
			'limit' => 1
		)
	);

	public $actsAs = array(
		'Ownable',
		'Postable.Postable' => array(
			'storageModel' => 'Activity'
		)
	);

	public function beforeDelete($cascade = true)
	{
		parent::beforeDelete($cascade);

		if($this->id) {
			// null out the associated photos
			$this->Asset->updateAll(
				array('Asset.album_id' => null),
				array('Asset.album_id' => $this->id)
			);
		}

		return true;
	}

	public function getAlbumCount($user = null)
	{
		return $this->find('count', array(
			'user_id' => $user
		));
	}

	public function getUserList($for_user)
	{
		$albums = $this->find('all', array(
			'fields' => 'Album.id, Album.title, Album.user_id, User.username',
			'contain' => 'User',
			'order' => 'User.username ASC, Album.title ASC',
			'conditions' => array(
				'OR' => array(
					'user_id' => $for_user,
					'shared' => true
				)
			)
		));
		return Hash::combine($albums, '{n}.Album.id', '{n}.Album.title', '{n}.User.username');
	}

	/**
	 * Converts the available Activity model and relationship data to reduce
	 * it to a human-friendly sentence.
	 *
	 * @param  {ActivityModel} $activity Activity to convert
	 */
	public function humanizeActivity(&$activity)
	{
		$activity['Activity']['phrase'] = ":user started a new album called “{$activity['Album']['title']}”";

		// inject the image count if we have it
		if(isset($activity['Album']['AssetCount'][0][0]['count']) && $activity['Album']['AssetCount'][0][0]['count']) {
			$activity['Activity']['phrase'] .= " with {$activity['Album']['AssetCount'][0][0]['count']} photos.";
		}

		$cover_asset = array();

		// pull the default album cover first
		if(!empty($activity['Album']['DefaultCover'][0]['id'])) {
			$cover_asset = $activity['Album']['DefaultCover'][0];
		}

		// replace with the user-selected album cover
		if(!empty($activity['Album']['Cover']['id'])) {
			$cover_asset = $activity['Album']['Cover'];
		}

		// attach a cover image if we located one
		if(!empty($cover_asset)) {
			$this->Asset->addMetaData($cover_asset);
			if(!empty($cover_asset['image-thumb'])) {
				$activity['Activity']['preview'] = $cover_asset['image-thumb'];
			}
			if(!empty($cover_asset['image-tiny'])) {
				$activity['Activity']['preview-small'] = $cover_asset['image-tiny'];
			}
		}

		$activity['Activity']['icon'] = 'photo-album-icon';
		$activity['Activity']['link'] = array('controller' => 'assets', 'action' => 'users', 'album' => $activity['Album']['id']);
	}
}
