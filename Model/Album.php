<?php

class Album extends AppModel {
	
	public $belongsTo = array(
		'Cover' => array(
			'className' => 'Asset',
			'foreignKey' => 'cover_id'
		)
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
}
