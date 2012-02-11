<?php

class Account extends AppModel {
	
	public function follow($handle) {
				
		$base_url = 'https://api.twitter.com/1/users/show.json';
		$params = array(
			'screen_name'=>$handle,
		);
		
		$record = $this->_readJson($base_url, $params);
		
		if(!empty($record['id'])) {
						
			$existing = $this->find('first',array(
				'conditions'=>array(
					'user_id'=>$record['id']
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
			
			$data['profile_image'] = $this->saveProfileImage($record['profile_image_url'], $record['screen_name']);
			
			$this->save($data);
		}
	}
	
	// keep a local copy of the profile image for display purposes
	public function saveProfileImage($url, $handle) {		
		// create curl resource
		$ch = curl_init();
		
		$relative_path = 'profile'.DS.'twitter'.DS.basename($url);
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