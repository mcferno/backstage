<?php
App::uses('Folder', 'Utility');
/**
 * Handles all tasks related to manipulation and management a user's site assets
 */
class AssetsController extends AppController {

	public $uses = array('Asset', 'Album');

	public $components = array(
		'Upload' => array(
			'mimeTypes' => array('image/png', 'image/jpeg', 'image/gif'),
			'fileExtensions' => array('png', 'jpg', 'jpeg', 'gif')
		)
	);

	public $paginate = array(
		'Asset' => array(
			'order' => 'Asset.created DESC',
			'limit' => 40,
			'maxLimit' => 1000
		),
		'Album' => array(
			'contain' => array(
				'Cover', 'DefaultCover', 'AssetCount', 'User'
			),
			'order' => 'Album.created DESC',
			'limit' => 10
		)
	);

	public function adminBeforeFilter() {

		if($this->RequestHandler->isMobile()) {
			$this->paginate['Asset']['limit'] = Configure::read('Site.Images.perPageMobile');
		} else {
			$this->paginate['Asset']['limit'] = Configure::read('Site.Images.perPage');
		}

		parent::adminBeforeFilter();
	}

	public function adminBeforeRender() {
		parent::adminBeforeRender();

		$model = $this->modelClass;
		if(isset($this->request->params['paging'])) {
			$models = array_keys($this->request->params['paging']);
			$model = reset($models);
		}

		$page_limits = array($this->paginate[$model]['limit'], $this->paginate[$model]['limit'] * 2, $this->paginate[$model]['limit'] * 4);

		if($this->RequestHandler->isMobile()) {
			$page_limits = array($this->paginate[$model]['limit'], $this->paginate[$model]['limit'] * 2, $this->paginate[$model]['limit'] * 3);
		}

		$this->set('page_limits', $page_limits);
	}

	/**
	 * Personalized asset index (for the current user)
	 */
	public function admin_index() {

		if(isset($this->request->params['named']['album'])) {

			// non existent album
			$this->Asset->Album->id = $this->request->params['named']['album'];
			if(!$this->Asset->Album->exists()) {
				$this->redirect(array('action' => $this->action));
			}

			// if not viewing a personal album, send users to the group album view
			if(!$this->Asset->Album->isOwner($this->Auth->user('id'), $this->request->params['named']['album'])) {
				$this->redirect(array('action' => 'users', 'album' => $this->request->params['named']['album']));
			}

		}

		$this->defaultPagination();
		$tag_conditions = array();

		// pull recent albums if we're not currently viewing one
		if(!isset($this->request->params['named']['album'])) {
			$albums = $this->Asset->Album->find('all', array(
				'contain' => array(
					'Cover', 'DefaultCover', 'AssetCount',
					'Asset' => array(
						'limit' => Configure::read('Site.Images.albumPreviews')
					)
				),
				'conditions' => array(
					'Album.user_id' => $this->Auth->user('id')
				),
				'limit' => Configure::read('Site.Images.recentAlbums'),
				'order' => 'Album.modified DESC'
			));
			$this->set('albums', $albums);

			$tag_conditions['Asset.user_id'] = $this->Auth->user('id');
			$this->paginate['Asset']['conditions']['Asset.user_id'] = $this->Auth->user('id');
		} else {
			$tag_conditions['Asset.album_id'] = $this->request->params['named']['album'];
		}

		$this->set('images', $this->paginate('Asset'));
		$this->set('tag_tally', $this->Asset->getTagTally($tag_conditions));
		$this->set('album_count', $this->Asset->Album->find('count', array('conditions' => array('user_id' => $this->Auth->user('id')))));
		$this->set('image_total', $this->Asset->find('count', array('conditions' => array('user_id' => $this->Auth->user('id')))));
	}

	public function admin_albums() {
		$this->defaultPagination();
		$this->paginate['Album']['contain']['Asset'] = array('limit' => Configure::read('Site.Images.albumPreviews'));
		$this->paginate['Album']['contain']['Asset']['order'] = 'created DESC';
		$this->set('albums', $this->paginate('Album'));
		$this->set('users', $this->Asset->User->find('list'));
	}

	/**
	 * Personalized asset index (for a specific user)
	 */
	public function admin_user($user_id = null) {
		if(empty($user_id)) {
			$this->redirect('admin_users');
		}
		if(Access::isOwner($user_id)) {
			$this->redirect(array('action' => 'admin_index'));
		}
		$this->paginate['Asset']['conditions']['Asset.user_id'] = $user_id;

		$this->defaultPagination();
		$this->set('tag_tally', $this->Asset->getTagTally(array('Asset.user_id' => $user_id)));
		$this->set('user', $this->Asset->User->findById($user_id));
		$this->set('images', $this->paginate('Asset'));
		$this->set('image_total', $this->Asset->find('count', array('conditions' => array('user_id' => $user_id))));
	}

	/**
	 * Assets and Albums from all users. Lists images, optionally groups by Album
	 */
	public function admin_users() {

		// if  viewing a personal album, send the user to their album page
		if(isset($this->request->params['named']['album'])) {

			// non existent album
			$this->Asset->Album->id = $this->request->params['named']['album'];
			if(!$this->Asset->Album->exists()) {
				$this->redirect(array('action' => 'albums'));
			}

			if($this->Asset->Album->isOwner($this->Auth->user('id'), $this->request->params['named']['album'])) {
				$this->redirect(array('action' => 'index', 'album' => $this->request->params['named']['album']));
			}
		}

		$this->paginate['Asset']['contain'][] = 'User';
		$contributingUsers = $this->Asset->find('all', array(
			'contain' => 'User',
			'group' => 'Asset.user_id'
		));

		$this->defaultPagination();

		// filter tags by the existing pagination filters, except tags themselves
		$tag_conditions = isset($this->paginate['Asset']['conditions']) ? $this->paginate['Asset']['conditions'] : array();
		unset($tag_conditions['Tagging.tag_id']);
		$this->set('tag_tally', $this->Asset->getTagTally($tag_conditions));

		$this->set('images', $this->paginate('Asset'));
		$this->set('image_total', $this->Asset->find('count'));
		$this->set('contributingUsers', $contributingUsers);
	}

	/**
	 * Generic pagination augmentation based on the existance of specific URL flag
	 *
	 * @param {Array} $options Pagination overrides
	 */
	protected function defaultPagination($options = array()) {

		// images with a specific tag
		$tag_filter = isset($options['tag']) ? $options['tag'] : (isset($this->request->params['named']['tag']) ? $this->request->params['named']['tag'] : false);
		if($tag_filter) {
			$tag = $this->Asset->Tag->findById($tag_filter);
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

		// images of a specific type
		$type_filter = isset($options['type']) ? $options['type'] : (isset($this->request->params['named']['type']) ? $this->request->params['named']['type'] : false);
		if($type_filter) {

			// special type converted to multiple types
			if($type_filter == 'Meme-Templates') {
				if(isset($this->paginate['Asset']['conditions'])) {
					$this->paginate['Asset']['conditions'] = array_merge($this->paginate['Asset']['conditions'], $this->Asset->getCleanImageConditions());
				} else {
					$this->paginate['Asset']['conditions'] = $this->Asset->getCleanImageConditions();
				}
			// standard single type lookup
			} else {
				$this->paginate['Asset']['conditions']['Asset.type'] = $type_filter;
			}
		}

		// images belonging to an album
		$album_filter = isset($options['album']) ? $options['album'] : (isset($this->request->params['named']['album']) ? $this->request->params['named']['album'] : false);
		if($album_filter) {
			$album = $this->Asset->Album->find('first', array(
				'contain' => array('User', 'AssetCount'),
				'conditions' => array(
					'Album.id' => $album_filter,
					'OR' => array(
						'Album.user_id' => $this->Session->read('Auth.User.id'),
						'Album.shared' => true
					)
				)
			));
			if(!empty($album)) {
				$this->set('album', $album);
				$this->set('upload_album', $album['Album']);
				$this->request->data = $album;
				$this->paginate['Asset']['conditions']['Asset.album_id'] = $album_filter;
			}
		}

		// images belonging to a specific user
		$user_filter = isset($options['user']) ? $options['user'] : (isset($this->request->params['named']['user']) ? $this->request->params['named']['user'] : false);
		if($user_filter) {
			$this->paginate['Asset']['conditions']['Asset.user_id'] = $user_filter;
			$this->paginate['Album']['conditions']['Album.user_id'] = $user_filter;
		}
	}

	/**
	 * Obtains Meme Generator-ready images via AJAX.
	 */
	public function admin_find() {
		$response = array();

		$this->paginate['Asset']['fields'] = array('id', 'user_id', 'filename');
		$this->paginate['Asset']['conditions'] = $this->Asset->getCleanImageConditions();
		$this->defaultPagination();

		$response['images'] = $this->paginate('Asset');
		$response['page'] = $this->request->params['paging']['Asset']['page'];
		$response['max_page'] = $this->request->params['paging']['Asset']['pageCount'];

		$this->set($response);
		$this->set('_serialize', array_keys($response));
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
				$asset_id = $this->Asset->id;
				$response['image_saved'] = true;
				$response['asset_id'] = $asset_id;

				// if a Contest entry, set the association
				if($type == 'Contest' && !empty($this->request->data['contestId'])) {
					ClassRegistry::init('AssetsContest')->save(array(
						'asset_id' => $asset_id,
						'contest_id' => $this->request->data['contestId']
					));

					// post this addition to the original thread
					if($this->Session->check('Auth.User.fb_target') && $this->User->hasFacebookAccess()) {
						$contest = $this->Asset->Contest->findById($this->request->data['contestId']);

						if(!empty($contest['Contest']['fb_id'])) {
							$asset_path = $this->Asset->getPath($asset_id);

							$result = $this->User->facebookApiCall(
								"/{$contest['Contest']['fb_id']}/comments",
								'POST',
								array(
									'attachment_url' =>  Router::url('/', true) . IMAGES_URL . $asset_path
								)
							);

							if(!empty($result['id'])) {
								$this->Asset->id = $asset_id;
								$this->Asset->saveField('fb_id', $result['id']);
							}
						}
					}

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

			$options = array();
			if(!empty($this->request->data['Asset']['album_id'])) {
				$options['album_id'] = $this->request->data['Asset']['album_id'];
			}

			// file upload
			if(!empty($this->request->data['Asset']['image']['name'])) {

				$valid = $this->Upload->isValidUpload($this->request->data['Asset']['image']);

				if($valid === true) {
					$save = $this->Asset->saveImage($this->request->data['Asset']['image']['tmp_name'], $this->Auth->user('id'), 'Upload', $options);

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
						$asset_id = $this->Asset->saveImage($file, $this->Auth->user('id'), 'URLgrab', $options);

						if($asset_id !== false) {
							$new_file .= $asset_id . '.' . $this->Upload->getExtension($this->request->data['Asset']['url']);
							rename($file, $new_file);

							$message = 'The image has been downloaded successfully!';
							$redirect = array('action' => 'view', $asset_id);
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
		$redirect = ($redirect) ? $redirect : array('action' => 'index');
		$this->redirect($redirect);
	}

	/**
	 * View an individual asset
	 *
	 * @param {UUID} $id Primary key of the desired asset
	 */
	public function admin_view($id = null) {

		$asset = $this->Asset->find('first', array(
			'contain' => array(
				'User', 'Tag', 'ContestEntry',
				'Album' => array(
					'conditions' => array(
						'OR' => array(
							'Album.user_id' => $this->Auth->user('id'),
							'Album.shared' => true
						)
					)
				)
			),
			'conditions' => array(
				'Asset.id' => $id
			)
		));

		// photo belongs to an album, fetch related photos
		if(!empty($asset['Album'])) {
			$this->paginate['Asset']['limit'] = 3;
			$this->paginate['Asset']['order'] = 'created DESC';

			$this->defaultPagination(array('album' => $asset['Album']['id']));

			$album_offset = $this->Asset->find('count', array(
				'conditions' => array(
					'album_id' => $asset['Album']['id'],
					'created >' => $asset['Asset']['created']
				)
			));
			$this->paginate['Asset']['offset'] = ($album_offset > 0) ? $album_offset - 1 : $album_offset;
			$this->set('album_offset', $album_offset);
			$this->set('album_images', $this->paginate('Asset'));
		}

		if(empty($asset)) {
			$this->Session->setFlash('Image could not be found.', 'messaging/alert-error');
			$this->redirect($this->referer('index'));
		}

		$this->request->data = $asset;

		$this->set('asset', $asset);
		$this->set('types', $this->Asset->getTypes());
		$this->set('tags', array_values($this->Asset->Tag->getListForModel('Asset')));
		$this->set('albums', $this->Asset->Album->getUserList($this->Session->read('Auth.User.id')));
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
					$this->Session->setFlash('The image has been cropped and saved.', 'messaging/alert-success');
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
			$this->Session->setFlash('Sorry, you can’t post this image at this time.', 'messaging/alert-error');
			$this->redirect($this->referer(array('action' => 'index')));
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
				$res = $fbSDK->api('/' . $this->Session->read('Auth.User.fb_target') . '/photos', 'POST', $imagePost);

				// post was successful, record the id for reference
				if(!empty($res['id'])) {
					$this->Asset->id = $id;
					$this->Asset->saveField('fb_id', $res['id']);

					$this->Session->setFlash('This image has been posted to Facebook.', 'messaging/alert-success');
					$this->redirect($this->referer(array('action' => 'view', $id)));
				}
			} catch (FacebookApiException $e) {}

			$this->Session->setFlash('An error occurred while attempting to post to Facebook.', 'messaging/alert-error');
			$this->redirect($this->referer(array('action' => 'view', $id)));
		}

		$redirectParams = array(
			'action' => 'post', $id
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
				$this->Session->setFlash('The image has been updated.', 'messaging/alert-success');
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
			$this->Session->setFlash('Image could not be found.', 'messaging/alert-error');
			$this->redirect($this->referer('index'));
		}

		if($this->Asset->delete($id)) {
			$this->Session->setFlash('The image has been deleted.', 'messaging/alert-success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Image could not be deleted.', 'messaging/alert-error');
		$this->redirect($this->referer('index'));
	}
}