<?php

class Album extends AppModel {
	
	public $belongsTo = array(
		'Cover' => array(
			'className' => 'Asset',
			'foreignKey' => 'cover_id'
		)
	);
	
	public $hasOne = array(
		'DefaultCover' => array(
			'className' => 'Asset',
			'foreignKey' => 'album_id'
		)
	);

	public $hasMany = array('Asset');

}