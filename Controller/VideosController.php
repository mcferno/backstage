<?php
/**
 * Handles all tasks related to manipulation and management a user's site assets
 */
class VideosController extends AppController {

	public $uses = array('Video', 'Message');

	public $components = array(
		'Upload' => array(
			'mimeTypes' => array('image/png', 'image/jpeg'),
			'fileExtensions' => array('png', 'jpg', 'jpeg')
		)
	);

	public $paginate = array(
		'Video' => array(
			'contain' => array('User', 'Tag'),
			'order' => 'Video.created DESC',
			'limit' => 10
		)
	);

	public function admin_index() {
		$this->defaultPagination();
		$this->set('tag_tally', $this->Video->getTagTally());
	}

	/**
	 * Prepares the necessary data for a paginated index of videos
	 */
	protected function defaultPagination() {
		// restrict videos to those by a specific tag
		if(isset($this->request->params['named']['tag'])) {
			$tag = $this->Video->Tag->findById($this->request->params['named']['tag']);
			$this->set('tag', $tag);

			$this->paginate['Video']['joins'][] = array(
				'alias' => 'Tagging',
				'type' => 'INNER',
				'table' => 'taggings',
				'conditions'=> array(
					'Video.id = Tagging.foreign_id',
					'Tagging.model' => 'Video'
				)
			);
			$this->paginate['Video']['group'] = 'Video.id';
			$this->paginate['Video']['conditions']['Tagging.tag_id'] = $tag['Tag']['id'];
		}

		// restrict videos to those by a specific user
		if(isset($this->request->params['named']['user'])) {
			$this->Message->User->id = $this->request->params['named']['user'];
			if($this->Message->User->exists()) {
				$this->paginate['Video']['conditions']['Video.user_id'] = $this->request->params['named']['user'];
				$this->set('user', $this->Message->User->findById($this->request->params['named']['user']));
			}
		}

		$videos = $this->paginate();

		// get message counts
		$tally = $this->Message->getTally(array(
			'foreign_id' => Hash::extract($videos, '{n}.Video.id'),
			'model' => 'Video'
		));

		$this->set('videos', $videos);
		$this->set('message_tally', $tally);
		$this->set('page_limits', array(20, 40, 80));
		$this->set('thumbnail_path', $this->Video->thumbnailPath);
	}

	public function admin_add() {
		if ($this->request->is('post')) {
			$this->Video->create();
			$this->Video->set('user_id', $this->Auth->user('id'));

			if ($this->Video->save($this->request->data)) {

				if(empty($this->request->data['Video']['url'])) {
					$this->Session->setFlash('Your video has been added! Please provide a screencap.', 'messaging/alert-success');
					$this->redirect(array('action' => 'image', $this->Video->id));
				} else {
					$this->Session->setFlash('Your video details has been saved. Please upload the video content.', 'messaging/alert-success');
					$this->redirect(array('action' => 'image', $this->Video->id));
				}
			} else {
				$this->Session->setFlash('Your new video could not be saved. Please, try again.', 'messaging/alert-error');
			}
		}
		
		$this->set('tags', array_values($this->Video->Tag->getListForModel('Video')));
	}

	/**
	 * Handles the lengthy video upload process
	 */
	public function admin_upload() {

	}

	/**
	 * Manages the creation of a thumbnail image. Allows the user to upload an
	 * image, or download one from a URL. Once an image is set, the user may
	 * choose the proper thumbnail crop from it.
	 * 
	 * @param {UUID} $id Link to set an image
	 */
	public function admin_image($id = null) {
		$this->Video->id = $id;
		if (!$this->Video->exists()) {
			throw new NotFoundException(__('Invalid video'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {

			// base file path of the eventual new image (missing extension)
			$new_file = "{$this->Video->thumbnailPath}/full/{$id}.";

			// file upload
			if(!empty($this->request->data['Video']['image']['name'])) {

				$valid = $this->Upload->isValidUpload($this->request->data['Video']['image']);

				if($valid === true) {
					$this->Upload->cleanPath(IMAGES . $new_file . '*'); // remove existing images
					$new_file .= $this->Upload->getExtension($this->request->data['Video']['image']['name']);
					move_uploaded_file($this->request->data['Video']['image']['tmp_name'], IMAGES . $new_file);
					
					$this->Session->setFlash('Image saved! Please crop the image below to complete the process.', 'messaging/alert-success');
					$this->redirect(array('action' => 'image', $id, 'mode' => 'crop'));
				} else {
					$this->Session->setFlash($valid, 'messaging/alert-error');
				}

			// URL grab
			} else {
				$valid = $this->Upload->isValidURL($this->request->data['Video']['url']);
				
				if($valid === true) {
					$this->Upload->cleanPath(IMAGES . $new_file . '*'); // remove existing images
					$new_file .= $this->Upload->getExtension($this->request->data['Video']['url']);
					$file = $this->Upload->saveURLtoFile($this->request->data['Video']['url'], IMAGES . $new_file);
					
					if($file !== false) {
						$this->Session->setFlash('Image saved! Please crop the image below to complete the process.', 'messaging/alert-success');
						$this->redirect(array('action' => 'image', $id, 'mode' => 'crop'));
					} else {
						$this->Session->setFlash('The URL could not be downloaded, please try again.','messaging/alert-error');
					}

				} else {
					$this->Session->setFlash($valid, 'messaging/alert-error');
				}
			}
		}

		$video = $this->Video->find('first', array(
			'contain' => array('User', 'Tag'),
			'conditions' => array(
				'Video.id' => $id
			)
		));
		$this->set('video', $video);
		$this->set('thumbnail_path', $this->Video->thumbnailPath);
	}

	public function admin_crop() {
		$response = array(
			'status' => 'failed'
		);

		if(!empty($this->data['image_id'])) {

			$this->Video->id = $this->data['image_id'];
			if($this->Video->exists()) {
				
				// remove existing thumbs before proceeding
				$this->Upload->cleanPath(IMAGES . "{$this->Video->thumbnailPath}/{$this->data['image_id']}.*");

				$status = $this->Video->saveThumbnail($this->data['image_id'], $this->data['coords']);
				if($status) {
					$this->Session->setFlash('The image has been cropped and saved.','messaging/alert-success');
					$response['status'] = 'success';
					$response['redirect'] = Router::url(array('controller' => 'videos', 'action' => 'view', $this->data['image_id']));
				}
			}
		}

		$this->set($response);
		$this->set('_serialize', array_keys($response));
	}

	public function admin_edit($id = null) {
		$this->Video->id = $id;
		if (!$this->Video->exists()) {
			throw new NotFoundException(__('Invalid video'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Video->save($this->request->data)) {
				$this->Session->setFlash('The video has been updated!', 'messaging/alert-success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('The video could not be updated. Please, try again.', 'messaging/alert-error');
			}
		} else {
			
			$this->request->data = $this->Video->find('first', array(
				'contain' => 'Tag',
				'conditions' => array(
					'Video.id' => $id
				)
			));
			$this->request->data['Video']['duration'] = $this->request->data['Video']['duration_nice'];
			
			// compile existing tags
			if(!empty($this->request->data['Tag'])) {
				$this->request->data['Tagging']['tags'] = implode(Hash::extract($this->request->data['Tag'], '{n}.name'), ',');
			}
		}

		$this->set('tags', array_values($this->Video->Tag->getListForModel('Video')));
	}

	public function admin_view($id = null) {
		$this->Video->id = $id;
		if (!$this->Video->exists()) {
			throw new NotFoundException(__('Invalid video'));
		}
		$video = $this->Video->find('first', array(
			'contain' => array('User'),
			'conditions' => array(
				'Video.id' => $id
			)
		));
		$this->set('video', $video);

		$tally = $this->Message->getTally(array(
			'foreign_id' => $video['Video']['id'],
			'model' => 'Video'
		));
		$this->set('message_tally', $tally);

		// owner
		if($this->Auth->user('id') == $video['Video']['user_id']) {
			$this->set('tag_tally', $this->Video->getTagTally($this->Auth->user('id')));
		} else {
			$this->set('tag_tally', $this->Video->getTagTally());
		}
		$this->set('thumbnail_path', $this->Video->thumbnailPath);
	}

	public function admin_delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Video->id = $id;
		if (!$this->Video->exists()) {
			throw new NotFoundException(__('Invalid video'));
		}

		if(!$this->isAdminUser() && !$this->Video->isOwner($this->Auth->user('id'))) {
			$this->Session->setFlash('Sorry, only the owner of this video can delete it!', 'messaging/alert-error');
			$this->redirect(array('action' => 'index'));
		}

		if ($this->Video->delete()) {
			$this->Session->setFlash('Your video has been removed!', 'messaging/alert-success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Your video could not be deleted. Please, try again.', 'messaging/alert-error');
		$this->redirect(array('action' => 'index'));
	}

}