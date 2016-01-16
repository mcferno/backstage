<?php

/**
 * Manages the creation and modification of Albums (groupings of images)
 */
class AlbumsController extends AppController
{
	/**
	 * Create or update Album meta-data
	 *
	 * @param string $id Album to modify
	 */
	public function admin_save($id = null)
	{
		$redirect = $this->referer(array('action' => 'index'));

		if($this->request->is('post') || $this->request->is('put')) {
			if(!empty($this->request->data['Album']['id'])) {
				$status = 'updated';
			} else {
				$status = 'created';

				// force user ownership
				$this->request->data['Album']['user_id'] = $this->Session->read('Auth.User.id');
				if(trim($this->request->data['Album']['title']) == '') {
					$this->request->data['Album']['title'] = 'Untitled Album';
				}
			}
			if($this->Album->save($this->request->data)) {
				if($status == 'created') {
					// view new album
					$redirect = array('controller' => 'assets', 'action' => 'index', 'album' => $this->Album->id);
				}
				$this->Session->setFlash('The Album has been ' . $status, 'messaging/alert-success');
			} else {
				$this->Session->setFlash("The album could not be {$status}. Please, try again.", 'messaging/alert-error');
			}
		}

		$this->redirect($redirect);
	}

	/**
	 * Set the Asset (image) to use as the Album cover
	 *
	 * @param string $album_id Album to affect
	 * @param string $asset_id Asset to associate as the Album's cover
	 */
	public function admin_set_cover($album_id = null, $asset_id)
	{
		$this->Album->id = $album_id;
		$this->Album->Asset->id = $asset_id;
		if($this->request->is('post') && $this->Album->exists() && $this->Album->Asset->exists()) {
			$this->Album->saveField('cover_id', $asset_id);
			$this->Session->setFlash('The image has been set as the album cover.', 'messaging/alert-success');
		}

		$this->redirect($this->referer(array('controller' => 'assets', 'action' => 'albums', 'user' => $this->Session->read('Auth.User.id'))));
	}

	/**
	 * Remove an Album, detaching its Asset associations
	 *
	 * @param string $id Album to remove
	 */
	public function admin_delete($id = null)
	{
		$this->Album->id = $id;
		if($this->Album->exists() && $this->Album->isOwner($this->Auth->user('id')) && $this->request->is('post')) {
			$this->Album->delete($id);
			$this->Session->setFlash('The album has been deleted.', 'messaging/alert-success');
		}
		$this->redirect($this->referer(array('controller' => 'assets', 'action' => 'albums', 'user' => $this->Auth->user('id'))));
	}

}