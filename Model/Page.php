<?php

/**
 * Controls dynamic page content
 */
class Page extends AppModel
{
	public $validate = array(
		'uri' => array(
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Page URI is already in use.',
				'allowEmpty' => false
			)
		)
	);
}