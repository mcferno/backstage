<?php
App::uses('Folder', 'Utility');
/**
 * Handles all tasks related to manipulation and management a user's site assets
 */
class AssetsController extends AppController {

	public $components = array(
		'Upload' => array(
			'mimeTypes' => array('image/png', 'image/jpeg', 'image/gif'),
			'fileExtensions' => array('png', 'jpg', 'jpeg', 'gif')
		)
	);

	public $paginate = array(
		'order' => 'Asset.created DESC',
		'limit' => 40,
		'maxLimit' => 150
	);
	
	public function adminBeforeFilter() {

		if($this->RequestHandler->isMobile()) {
			$this->paginate['limit'] = 15;
		}

		parent::adminBeforeFilter();
	}

	public function adminBeforeRender() {
		parent::adminBeforeRender();

		$page_limits = array($this->paginate['limit'], 80, 150);

		if($this->RequestHandler->isMobile()) {
			$page_limits = array($this->paginate['limit'], 30, 60);
		}

		$this->set('page_limits', $page_limits);
	}
	
	/**
	 * Personalized asset index (for the current user)
	 */
	public function admin_index() {
		$this->defaultPagination();
		$this->paginate['conditions']['Asset.user_id'] = $this->Auth->user('id');
		$this->set('tag_tally', $this->Asset->getTagTally($this->paginate['conditions']['Asset.user_id']));
		$this->set('images', $this->paginate());
		$this->set('image_total', $this->Asset->find('count', array('conditions' => array('user_id' => $this->Auth->user('id')))));
	}
	
	/**
	 * Personalized asset index (for a specific user)
	 */
	public function admin_user($user_id = null) {
		if(empty($user_id)) {
			$this->redirect('admin_users');
		}
		if(Access::isOwner($user_id)) {
			$this->redirect(array('action'=>'admin_index'));
		}
		$this->paginate['conditions']['Asset.user_id'] = $user_id;
		
		$this->defaultPagination();
		$this->set('tag_tally', $this->Asset->getTagTally($user_id));
		$this->set('user',$this->Asset->User->findById($user_id));
		$this->set('images',$this->paginate());
		$this->set('image_total', $this->Asset->find('count', array('conditions' => array('user_id' => $user_id))));
	}
	
	/**
	 * Assets from all users
	 */
	public function admin_users() {
		$paginate = array(
			'contain' => 'User',
			'order' => 'Asset.created DESC'
		);
		$contributingUsers = $this->Asset->find('all',array(
			'contain' => 'User',
			'group' => 'Asset.user_id'
		));

		$this->defaultPagination();
		$this->set('tag_tally', $this->Asset->getTagTally());
		$this->paginate = array_merge($this->paginate, $paginate);
		$this->set('images',$this->paginate());
		$this->set('image_total', $this->Asset->find('count'));
		$this->set('contributingUsers',$contributingUsers);
	}

	/**
	 * Generic pagination augmentation based on the existance of specific URL flag
	 */
	protected function defaultPagination() {

		// tagged asset filtering
		if(isset($this->request->params['named']['tag'])) {
			$tag = $this->Asset->Tag->findById($this->request->params['named']['tag']);
			$this->set('tag', $tag);

			$this->paginate['Asset']['joins'][] = array(
				'alias' => 'Tagging',
				'type' => 'INNER',
				'table' => 'taggings',
				'conditions'=> array(
					'Asset.id = Tagging.foreign_id',
					'Tagging.model' => 'Asset'
				)
			);
			$this->paginate['Asset']['group'] = 'Asset.id';
			$this->paginate['Asset']['conditions']['Tagging.tag_id'] = $tag['Tag']['id'];
		}
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

					$response['view_url'] = Router::url(array('controller' => 'contests', 'action' => 'view', $this->request->data['contestId'], 'page' => 1), true);
				} else {
					$response['view_url'] = Router::url(array('controller' => 'assets', 'action' => 'view', $this->Asset->id), true);
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
		$redirect = false;
		$message = false;
		$error = false;
		if ($this->request->is('post') || $this->request->is('put')) {

			// base file path of the eventual new image original
			$new_file = $this->Asset->getFolderPath($this->Auth->user('id')) . 'full' . DS;
			if(!file_exists($new_file)) {
				$dir = new Folder($new_file, true, 0755);
			}

			// file upload
			if(!empty($this->request->data['Asset']['image']['name'])) {

				$valid = $this->Upload->isValidUpload($this->request->data['Asset']['image']);

				if($valid === true) {
					$save = $this->Asset->saveImage($this->request->data['Asset']['image']['tmp_name'], $this->Auth->user('id'), 'Upload');

					// save is the new model ID
					if($save !== false) {

						$new_file .= $save . '.' . $this->Upload->getExtension($this->request->data['Asset']['image']['name']);
						move_uploaded_file($this->request->data['Asset']['image']['tmp_name'], $new_file);

						$message = 'The image has been uploaded successfully!';
						$redirect = array('action' => 'view', $save);
					
					} else {
						$message = 'Image processing has failed, please try again.';
						$error = true;
					}

				} else {
					$message = $valid;
					$error = true;
				}

			// URL grab
			} else {
				$valid = $this->Upload->isValidURL($this->request->data['Asset']['url']);
				
				if($valid === true) {
					$file = $this->Upload->saveURLtoFile($this->request->data['Asset']['url']);
					
					if($file !== false) {
						$asset_id = $this->Asset->saveImage($file, $this->Auth->user('id'), 'URLgrab');

						if($asset_id !== false) {
							$new_file .= $asset_id . '.' . $this->Upload->getExtension($this->request->data['Asset']['url']);
							rename($file, $new_file);

							$message = 'The image has been downloaded successfully!';
							$redirect = array('action'=>'view', $asset_id);
						} else {
							$message = 'Image processing has failed, please try again.';
							$error = true;
						}

					} else {
						$message = 'The URL could not be downloaded, please try again.';
						$error = true;
					}

				} else {
					$message = $valid;
					$error = true;
				}
			}
		}

		if($this->request->is('ajax')) {
			if($redirect) {
				$redirect = Router::url($redirect);
			}
			$response = compact('error', 'redirect', 'message');
			$this->set($response);
			$this->set('_serialize', array_keys($response));

			// JS redirect will reveal this message
			if($error === false) {
				$this->Session->setFlash($message, 'messaging/alert-success');
			}
			return;
		}

		$this->Session->setFlash($message, ($error === false) ? 'messaging/alert-success' : 'messaging/alert-error');
		$redirect = ($redirect) ? $redirect : array('action'=>'index');
		$this->redirect($redirect);
	}
	
	/**
	 * View an individual asset
	 *
	 * @param {UUID} $id Primary key of the desired asset
	 */
	public function admin_view($id = null) {

		$asset = $this->Asset->find('first', array(
			'contain' => array('User', 'Tag'),
			'conditions' => array(
				'Asset.id' => $id
			)
		));

		if(empty($asset)) {
			$this->Session->setFlash('Image could not be found.','messaging/alert-error');
			$this->redirect($this->referer('index'));
		}

		$this->request->data = $asset;

		$this->set('asset', $asset);
		$this->set('types', $this->Asset->getTypes());
		$this->set('tags', array_values($this->Asset->Tag->getListForModel('Asset')));
		$this->request->data['Tagging']['tags'] = implode(Hash::extract($this->request->data['Tag'], '{n}.name'), ',');
	}

	/**
	 * Crops an existing image, saving it as a new image by the user who initiated
	 * the crop.
	 */
	public function admin_crop() {
		$response = array(
			'status' => 'failed'
		);

		if(!empty($this->data['image_id'])) {

			$this->Asset->id = $this->data['image_id'];
			if($this->Asset->exists()) {
				$asset = $this->Asset->read();
				$image_path = IMAGES . $this->Asset->getPath($this->Asset->id);

				$status = $this->Asset->saveImage($image_path, $this->Auth->user('id'), 'Crop', array('crop' => $this->data['coords']));
				if($status) {
					$this->Session->setFlash('The image has been cropped and saved.','messaging/alert-success');
					$response['status'] = 'success';
					$response['redirect'] = Router::url(array('controller' => 'assets', 'action' => 'view', $this->Asset->id));
				}
			}
		}

		$this->set($response);
		$this->set('_serialize', array_keys($response));
	}

	/**
	 * Posts an image as a comment to the Group Chat, and visit the Chat.
	 * Utility shortcut.
	 */
	public function admin_chat_post($id = null) {
		$this->Asset->id = $id;

		if(!empty($id) && $this->Asset->exists()) {
			$url = Router::url('/'.IMAGES_URL . $this->Asset->getPath($id), true);
			ClassRegistry::init('Message')->save(array(
				'model' => 'Chat',
				'user_id' => $this->Auth->user('id'),
				'text' => $url
			));
			$this->redirect(array('controller' => 'users', 'action' => 'group_chat'));
		}
		$this->redirect($this->referer(array('controller' => 'users', 'action' => 'group_chat')));
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

	public function admin_edit($id) {
		if(!empty($this->request->data)) {
			if($this->Asset->save($this->request->data, false)) {
				$this->Session->setFlash('Asset type successfully updated.', 'messaging/alert-success');
			} else {
				$this->Session->setFlash('An error occured while saving. Please try again.', 'messaging/alert-error');
			}
		}
		$this->redirect($this->referer(array('action' => 'admin_view', $id)));
	}
	
	/**
	 * Delete a user's image
	 *
	 * @param {UUID} $id Primary key of the desired asset
	 */
	public function admin_delete($id = null) {
		if(empty($id) || !$this->Asset->isOwner($this->Auth->user('id'), $id)) {
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