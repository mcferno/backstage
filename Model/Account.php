<?php
/**
 * Social accounts monitored by the app for content mirroring
 */
class Account extends AppModel {

	/**
	 * Tracks a Twitter user by storing their latest profile information and
	 * profile image.
	 *
	 * @param string $handle Twitter account name to track
	 */
	public function follow($handle) {

		$params = array(
			'screen_name' => $handle,
		);

		$record = ClassRegistry::init('Twitter')->getAPIObject()->users_show($params, true);

		if(!empty($record['id'])) {

			$existing = $this->find('first', array(
				'conditions' => array(
					'user_id' => $record['id']
				)
			));

			$this->create();

			// found an existing record, update it
			if(!empty($existing[$this->alias]['user_id'])) {
				$this->id = $existing[$this->alias][$this->primaryKey];
			}

			$data = array(
				'handle' => $record['screen_name'],
				'user_id' => $record['id'],
				'data' => json_encode($record)
			);

			$data['profile_image'] = $this->saveProfileImage($record['profile_image_url']);

			$this->save($data);
		}
	}

	/**
	 * Keep a local copy of the profile image for display purposes
	 *
	 * @param string $url URL of the profile image we wish to save.
	 * @return string Relative path to the stored image.
	 */
	public function saveProfileImage($url) {
		// create curl resource
		$ch = curl_init();

		$relative_path = 'profile' . DS . 'twitter' . DS . basename($url);
		$file = fopen(IMAGES . $relative_path, "w");

		// set url
		curl_setopt($ch, CURLOPT_URL, $url);

		//return the transfer as a file
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FILE, $file);

		// $output contains the output string
		curl_exec($ch);
		curl_close($ch);
		fclose($file);

		return $relative_path;
	}
}