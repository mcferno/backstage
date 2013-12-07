<?php
/**
 * Activity :: represents user and system driven updates across all relevant
 * model types. Serves as the base for all user notifications.
 */
class Activity extends AppModel {

	public $order = 'Activity.created DESC';

	public $belongsTo = array(

		// the user who triggered the action (optional)
		'User',

		// the data the 'activity' corresponds to
		'Album' => array('foreignKey' => 'foreign_key'),
		'Asset' => array('foreignKey' => 'foreign_key'),
		'Contest' => array('foreignKey' => 'foreign_key'),
		'Message' => array('foreignKey' => 'foreign_key'),
		'Link' => array('foreignKey' => 'foreign_key'),
		'Video' => array('foreignKey' => 'foreign_key')
	);

	public function afterFind($results, $primary = false) {
		$results = parent::afterFind($results, $primary);

		// inject human friendly strings
		foreach ($results as &$result) {
			if(isset($result['Activity']['model']) && isset($result[$result['Activity']['model']])) {
				$this->{$result['Activity']['model']}->humanizeActivity($result);
			}
		}

		return $results;
	}

	public function countNewActivity($user_id, $since = false) {
		if($since === false) {
			$since = $this->User->field('last_update', array('id' => $user_id));
			
			// since values was not found, or exceeds the max elapsed time.
			if($since === false) {
				$since = date(MYSQL_DATE_FORMAT, 0);
			}
		}	
		return $this->find('count',array(
			'conditions'=>array(
				'user_id <>' => $user_id,
				'created >=' => $since
			)
		));
	}
}