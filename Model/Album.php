<?php

class Album extends AppModel {
	
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

	public function getUserList($for_user) {
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
}
