<?php
/**
 * Handles all tasks related to manipulation and management a user's site assets
 */
App::uses('Validation', 'Utility');

class AssetsController extends AppController {

	public $paginate = array(
		'order' => 'Asset.created DESC',
		'limit' => 12
	);

	// image formats permitted by image manipulation functions
	public $permittedImageTypes = array(
		'png' => 'image/png',
		'gif' => 'image/gif',
		'jpg' => 'image/jpeg',
		'jpeg' => 'image/jpeg'
	);
	
	public function adminBeforeFilter() {		
		if($this->request->is('ajax')) {
			$this->disableCache(); // expire cache immediately
			$this->RequestHandler->renderAs($this, 'json');
			$this->Security->validatePost = false;
			$this->Security->csrfCheck = false;
		}
		parent::adminBeforeFilter();	
	}
	
	/**
	 * Personalized asset index (for the current user)
	 */
	public function admin_index() {
		$this->paginate['conditions']['Asset.user_id'] = $this->Auth->user('id');
		$this->set('images',$this->paginate());
		$this->set('user_dir','user/'.$this->Auth->user('id').'/');
	}
	
	/**
	 * Personalized asset index (for a specific user)
	 */
	public function admin_user($user_id = null) {
		if(empty($user_id)) {
			$this->redirect('admin_users');
		}
		if($user_id == $this->Auth->user('id')) {
			$this->redirect(array('action'=>'admin_index'));
		}
		$this->paginate['conditions']['Asset.user_id'] = $user_id;
		
		$this->set('user',$this->Asset->User->findById($user_id));
		$this->set('images',$this->paginate());
		$this->set('user_dir','user/'.$user_id.'/');
	}
	
	public function admin_users() {
		$paginate = array(
			'contain' => 'User',
			'order' => 'Asset.created DESC'
		);
		$contributingUsers = $this->Asset->find('all',array(
			'contain' => 'User',
			'group' => 'Asset.user_id'
		));
		$this->paginate = array_merge($this->paginate, $paginate);
		$this->set('images',$this->paginate());
		$this->set('contributingUsers',$contributingUsers);
		$this->set('user_dir','user/');
	}
	
	/**
	 * Saves ajax posted image data
	 */
	public function admin_save() {

		$response = array('image_saved' => false);
		
		// process upload
		if(!empty($this->request->data['image'])) {

			$type = (!empty($this->request->data['type'])) ? $this->request->data['type'] : 'Meme';
			$status = $this->Asset->saveEncodedImage($this->request->data['image'], $this->Auth->user('id'), $type);
			
			if($status === true) {
				$response['image_saved'] = true;
				$response['asset_id'] = $this->Asset->id;

				// if a Contest entry, set the association
				if($type == 'Contest' && !empty($this->request->data['contestId'])) {
					ClassRegistry::init('AssetsContest')->save(array(
						'asset_id' => $this->Asset->id,
						'contest_id' => $this->request->data['contestId']
					));
				}
			}
		}
		
		$this->set($response);
		$this->set('_serialize', array_keys($response));
	}
	
	/**
	 * Saves single file uploads
	 */
	public function admin_upload() {

		// file has been posted
		if(!empty($this->request->data['Asset']['image']['name'])) {
			
			// upload error
			if($this->request->data['Asset']['image']['error'] !== 0 || !file_exists($this->request->data['Asset']['image']['tmp_name'])) {
				$this->Session->setFlash('Image upload has failed, please try again.','messaging/alert-error');
				
			// mine-type error
			} elseif(!in_array($this->request->data['Asset']['image']['type'],array('image/jpeg','image/png'))){
				$this->Session->setFlash('Sorry, JPEG and PNG uploads only.','messaging/alert-error');
				
			// no errors found, process image
			} else {
				$save = $this->Asset->saveImage($this->request->data['Asset']['image']['tmp_name'], $this->Auth->user('id'), 'Upload');
				
				if($save === false) {
					$this->Session->setFlash('Image processing has failed, please try again.','messaging/alert-error');
					
				// save is the new model ID
				} else {
					$this->Session->setFlash('The image has been uploaded successfully!','messaging/alert-success');
					$this->redirect(array('action'=>'view', $save));
				}
			}

		// url to scrape
		} elseif (!empty($this->request->data['Asset']['url'])) {
			
			$target_url = $this->request->data['Asset']['url'];

			if(Validation::url($target_url)) {

				$url_parts = parse_url($target_url);
				$extension_regex = implode('|', array_keys($this->permittedImageTypes));

				// restrict url capture to image formats
				if(preg_match('/\.(' . $extension_regex . ')$/i', $url_parts['path'])) {

					// attempt download
					$file = $this->saveURLtoTemp($target_url, array_values($this->permittedImageTypes));

					if($file !== false) {
						$asset_id = $this->Asset->saveImage($file, $this->Auth->user('id'), 'URLgrab');
						unlink($file);

						if($asset_id !== false) {
							$this->Session->setFlash('The image has been downloaded successfully!','messaging/alert-success');
							$this->redirect(array('action'=>'view', $asset_id));
						} else {
							$this->Session->setFlash('Image processing has failed, please try again.','messaging/alert-error');
						}

					} else {
						unlink($file);
						$this->Session->setFlash('The URL could not be downloaded, please try again.','messaging/alert-error');
					}

				// non-image url provided
				} else {
					$this->Session->setFlash('Only png, gif, and jpg/jpeg are accepted via URL, please try a different URL.','messaging/alert-error');
				}

			} else {
				$this->Session->setFlash('Invalid image URL, please try again.','messaging/alert-error');
			}
		}
		
		$this->redirect(array('action'=>'index'));
	}
	
	/**
	 * View an individual asset
	 *
	 * @param {UUID} $id Primary key of the desired asset
	 */
	public function admin_view($id = null) {
		$asset = $this->Asset->find('first',array(
			'contain' => 'User',
			'conditions' => array(
				'Asset.id' => $id,
				// 'Asset.user_id'=>$this->Auth->user('id')
			)
		));
		if(empty($asset)) {
			$this->Session->setFlash('Image could not be found.','messaging/alert-error');
			$this->redirect($this->referer('index'));
		}
		$this->set('asset',$asset);	
		$this->set('user_dir','user/'.$asset['Asset']['user_id'].'/');
	}
	
	/**
	 * Posts a single image to a specific Facebook group
	 *
	 * @param {UUID} $id Primary key of the actionable asset
	 */
	public function admin_post($id = null) {
		$asset = $this->Asset->hasAny(array(
			'Asset.id' => $id,
			'Asset.user_id' => $this->Auth->user('id')
		));
		
		// only owners of the image and users who are cleared for fb integration can continue
		if($asset !== true || $this->Session->check('Auth.User.fb_target') === false) {
			$this->Session->setFlash('Sorry, you can\'t post this image at this time.','messaging/alert-error');
			$this->redirect($this->referer(array('action'=>'index')));
		}
		
		$fbSDK = $this->User->getFacebookObject();
		
		// verify active FB user session
		if($fbSDK->getUser()) {
			
			$imagePost = $this->Asset->castToFacebook($id);
			
			// attach optional message
			if(!empty($this->request->query['message'])) {
				$imagePost['message'] = $this->request->query['message'];
			}
			
			try {
				// post to the api (upload)
				$fbSDK->setFileUploadSupport(true);
				$res = $fbSDK->api('/'.$this->Session->read('Auth.User.fb_target').'/photos','POST',$imagePost);
				
				// post was successful, record the id for reference
				if(!empty($res['id'])) {
					$this->Asset->id = $id;
					$this->Asset->saveField('fb_id',$res['id']);
					
					$this->Session->setFlash('This image has been posted to Facebook.','messaging/alert-success');
					$this->redirect($this->referer(array('action'=>'view',$id)));
				}
			} catch (FacebookApiException $e) {}
			
			$this->Session->setFlash('An error occurred while attempting to post to Facebook.','messaging/alert-error');
			$this->redirect($this->referer(array('action'=>'view',$id)));
		}
		
		$redirectParams = array(
			'action'=>'post', $id
		);
		if(!empty($this->request->query['message'])) {
			$redirectParams['?'] = array('message' => $this->request->query['message']);
		}
		
		// send the user away to authenticate
		$login_params = array(
			'scope' => $this->User->getFacebookPermissions(),
			'redirect_uri' => Router::url($redirectParams,true)
		);
		
		$this->redirect($fbSDK->getLoginUrl($login_params));
	}
	
	/**
	 * Delete a user's image
	 *
	 * @param {UUID} $id Primary key of the desired asset
	 */
	public function admin_delete($id = null) {
		if(empty($id) || !$this->Asset->hasAny(array('id'=>$id,'user_id'=>$this->Auth->user('id')))) {
			$this->Session->setFlash('Image could not be found.','messaging/alert-error');
			$this->redirect($this->referer('index'));
		}
		
		if($this->Asset->delete($id)) {
			$this->Session->setFlash('The image has been deleted.','messaging/alert-success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash('Image could not be deleted.','messaging/alert-error');
		$this->redirect($this->referer('index'));
	}
}