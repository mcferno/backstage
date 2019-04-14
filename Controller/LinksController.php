<?php
App::uses('Folder', 'Utility');

class LinksController extends AppController
{
	public $uses = array('Link', 'Message');

	public $components = array(
		'Upload' => array(
			'mimeTypes' => array('image/png', 'image/jpeg'),
			'fileExtensions' => array('png', 'jpg')
		)
	);

	public $paginate = array(
		'Link' => array(
			'contain' => array('User', 'Tag'),
			'limit' => 10,
			'order' => 'Link.created DESC'
		)
	);

	public function adminBeforeRender()
	{
		parent::adminBeforeRender();
		$this->set('title', 'Links');
	}

	public function admin_index()
	{
		// show sticky posts at the top if the order is not manually set
		if(!isset($this->request->params['named']['sort'])) {
			$this->paginate['Link']['order'] = 'Link.sticky DESC, Link.created DESC';
		}
		$this->defaultPagination();
		$this->set('tag_tally', $this->Link->getTagTally());
	}

	public function admin_my_links()
	{
		$this->paginate['Link']['conditions']['Link.user_id'] = $this->Auth->user('id');
		$this->set('sectionTitle', 'My Links');
		$this->defaultPagination();
		$this->set('tag_tally', $this->Link->getTagTally(array('Link.user_id' => $this->Auth->user('id'))));
		$this->render('admin_index');
	}

	/**
	 * Prepares the necessary data for a paginated index of links
	 */
	protected function defaultPagination()
	{
		// restrict links to those by a specific tag
		if(isset($this->request->params['named']['tag'])) {
			$tag = $this->Link->Tag->findById($this->request->params['named']['tag']);
			$this->set('tag', $tag);

			$this->paginate['Link']['joins'][] = array(
				'alias' => 'Tagging',
				'type' => 'INNER',
				'table' => 'taggings',
				'conditions' => array(
					'Link.id = Tagging.foreign_id',
					'Tagging.model' => 'Link'
				)
			);
			$this->paginate['Link']['group'] = 'Link.id';
			$this->paginate['Link']['conditions']['Tagging.tag_id'] = $tag['Tag']['id'];
		}

		// restrict links to those by a specific user
		if(isset($this->request->params['named']['user'])) {
			$this->Message->User->id = $this->request->params['named']['user'];
			if($this->Message->User->exists()) {
				$this->paginate['Link']['conditions']['Link.user_id'] = $this->request->params['named']['user'];
				$this->set('user', $this->Message->User->findById($this->request->params['named']['user']));
			}
		}

		$links = $this->paginate();

		// get message counts
		$tally = $this->Message->getTally(array(
			'foreign_id' => Hash::extract($links, '{n}.Link.id'),
			'model' => 'Link'
		));

		$this->set('links', $links);
		$this->set('message_tally', $tally);
		$this->set('page_limits', array(20, 40, 80));
		$this->set('thumbnail_path', $this->Link->thumbnailPath);
	}

	public function admin_view($id = null)
	{
		$this->Link->id = $id;
		if(!$this->Link->exists()) {
			throw new NotFoundException(__('Invalid link'));
		}
		$link = $this->Link->find('first', array(
			'contain' => array('User', 'Tag'),
			'conditions' => array(
				'Link.id' => $id
			)
		));
		$this->set('link', $link);

		$tally = $this->Message->getTally(array(
			'foreign_id' => $link['Link']['id'],
			'model' => 'Link'
		));
		$this->set('message_tally', $tally);

		// owner
		if(Access::isOwner($link['Link']['user_id'])) {
			$this->set('tag_tally', $this->Link->getTagTally(array('Link.user_id' => $this->Auth->user('id'))));
		} else {
			$this->set('tag_tally', $this->Link->getTagTally());
		}
		$this->set('thumbnail_path', $this->Link->thumbnailPath);
	}

	public function admin_add()
	{
		if($this->request->is('post')) {
			$this->Link->create();
			$this->Link->set('user_id', $this->Auth->user('id'));

			if($this->Link->save($this->request->data)) {
				$this->Session->setFlash('Your new link has been added!', 'messaging/alert-success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Your new link could not be saved. Please, try again.', 'messaging/alert-error');
			}
		}

		$this->set('tags', array_values($this->Link->Tag->getListForModel('Link')));
	}

	public function admin_edit($id = null)
	{
		$this->Link->id = $id;
		if(!$this->Link->exists()) {
			throw new NotFoundException(__('Invalid link'));
		}
		if($this->request->is('post') || $this->request->is('put')) {
			if($this->Link->save($this->request->data)) {
				$this->Session->setFlash('The link has been updated!', 'messaging/alert-success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('The link could not be updated. Please, try again.', 'messaging/alert-error');
			}
		} else {

			$this->request->data = $this->Link->find('first', array(
				'contain' => 'Tag',
				'conditions' => array(
					'Link.id' => $id
				)
			));

			// compile existing tags
			if(!empty($this->request->data['Tag'])) {
				$this->request->data['Tagging']['tags'] = implode(Hash::extract($this->request->data['Tag'], '{n}.name'), ',');
			}
		}

		$this->set('tags', array_values($this->Link->Tag->getListForModel('Link')));
	}

	/**
	 * Manages the creation of a thumbnail image. Allows the user to upload an
	 * image, or download one from a URL. Once an image is set, the user may
	 * choose the proper thumbnail crop from it.
	 *
	 * @param string $id Link to set an image
	 */
	public function admin_image($id = null)
	{
		$this->Link->id = $id;
		if(!$this->Link->exists()) {
			throw new NotFoundException(__('Invalid link'));
		}

		if($this->request->is('post') || $this->request->is('put')) {

			// base file path of the eventual new image (missing extension)
			$new_file = "{$this->Link->thumbnailPath}/full/{$id}.";
			$baseDir = IMAGES . dirname($new_file);

			// ensure the base directory exists
			if(!file_exists($baseDir)) {
				$dir = new Folder($baseDir, true, 0755);
			}

			// file upload
			if(!empty($this->request->data['Link']['image']['name'])) {

				$valid = $this->Upload->isValidUpload($this->request->data['Link']['image']);

				if($valid === true) {
					$this->Upload->cleanPath(IMAGES . $new_file . '*'); // remove existing images
					$new_file .= $this->Upload->getExtension($this->request->data['Link']['image']['name']);
					move_uploaded_file($this->request->data['Link']['image']['tmp_name'], IMAGES . $new_file);

					$this->Session->setFlash('Image saved! Please crop the image below to complete the process.', 'messaging/alert-success');
					$this->redirect(array('action' => 'image', $id, 'mode' => 'crop'));
				} else {
					$this->Session->setFlash($valid, 'messaging/alert-error');
				}

			// URL grab
			} else {
				$valid = $this->Upload->isValidURL($this->request->data['Link']['url']);

				if($valid === true) {
					$this->Upload->cleanPath(IMAGES . $new_file . '*'); // remove existing images
					$new_file .= $this->Upload->getExtension($this->request->data['Link']['url']);
					$file = $this->Upload->saveURLtoFile($this->request->data['Link']['url'], IMAGES . $new_file);

					if($file !== false) {
						$this->Session->setFlash('Image saved! Please crop the image below to complete the process.', 'messaging/alert-success');
						$this->redirect(array('action' => 'image', $id, 'mode' => 'crop'));
					} else {
						$this->Session->setFlash('The URL could not be downloaded, please try again.', 'messaging/alert-error');
					}

				} else {
					$this->Session->setFlash($valid, 'messaging/alert-error');
				}
			}
		}

		$link = $this->Link->find('first', array(
			'contain' => array('User', 'Tag'),
			'conditions' => array(
				'Link.id' => $id
			)
		));
		$this->set('link', $link);
		$this->set('thumbnail_path', $this->Link->thumbnailPath);
	}

	public function admin_crop()
	{
		$response = array(
			'status' => 'failed'
		);

		if(!empty($this->data['image_id'])) {

			$this->Link->id = $this->data['image_id'];
			if($this->Link->exists()) {

				// remove existing thumbs before proceeding
				$this->Upload->cleanPath(IMAGES . "{$this->Link->thumbnailPath}/{$this->data['image_id']}.*");

				$status = $this->Link->saveThumbnail($this->data['image_id'], $this->data['coords']);
				if($status) {
					$this->Session->setFlash('The image has been cropped and saved.', 'messaging/alert-success');
					$response['status'] = 'success';
					$response['redirect'] = Router::url(array('controller' => 'links', 'action' => 'view', $this->data['image_id']));
				}
			}
		}

		$this->set($response);
		$this->set('_serialize', array_keys($response));
	}

	public function admin_delete($id = null)
	{
		if(!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Link->id = $id;
		if(!$this->Link->exists()) {
			throw new NotFoundException(__('Invalid link'));
		}

		if(!Access::hasRole('Admin') && !$this->Link->isOwner($this->Auth->user('id'))) {
			$this->Session->setFlash('Sorry, only the owner of this link can delete it!', 'messaging/alert-error');
			$this->redirect(array('action' => 'index'));
		}

		if($this->Link->delete()) {
			$this->Session->setFlash('Your link has been removed!', 'messaging/alert-success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Your link could not be deleted. Please, try again.', 'messaging/alert-error');
		$this->redirect(array('action' => 'index'));
	}
}
