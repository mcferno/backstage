<?php
App::uses('AppModel', 'Model');

class Link extends AppModel {

	public $displayField = 'title';
	public $order = array('Link.created' => 'DESC');

	public $belongsTo = array('User');
	public $actsAs = array('Taggable', 'Ownable');
	public $hasAndBelongsToMany = array(
		'Tag' => array(
			'joinTable' => 'taggings',
			'foreignKey' => 'foreign_id'
		)
	);
}
