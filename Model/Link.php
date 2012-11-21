<?php
App::uses('AppModel', 'Model');

class Link extends AppModel {

	public $displayField = 'title';
	public $order = array('Link.created' => 'DESC');

	public $belongsTo = array('User');
	public $actsAs = array(
		'Taggable', 
		'Ownable',
		'Postable.Postable' => array(
			'storageModel' => 'Activity'
		)
	);
	public $hasAndBelongsToMany = array(
		'Tag' => array(
			'joinTable' => 'taggings',
			'foreignKey' => 'foreign_id'
		)
	);

	public function humanizeActivity(&$link) {
		$link['Activity']['phrase'] = ":user added a new link";
		if(!empty($link['Link']['title'])) {
			$link['Activity']['phrase'] .= " called \"{$link['Link']['title']}\".";
		}
		$link['Activity']['icon'] = 'application-browser';
		$link['Activity']['link'] = array('controller' => 'links', 'action' => 'view', $link['Link']['id']);
	}
}
