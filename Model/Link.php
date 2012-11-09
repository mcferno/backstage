<?php
App::uses('AppModel', 'Model');

class Link extends AppModel {

	public $displayField = 'title';
	public $order = array('Link.created' => 'DESC');

	public $belongsTo = array('User');
}
