<?php
/**
 * Handles all tasks related to manipulation and management a user's site assets
 */
class VideosController extends AppController {

	public $components = array(
		'Upload' => array(
			'mimeTypes' => array('image/png', 'image/jpeg', 'image/gif'),
			'fileExtensions' => array('png', 'jpg', 'jpeg', 'gif')
		)
	);

	public $paginate = array(
		'order' => 'Video.created DESC',
		'contain' => 'User'
	);

	public function admin_index() {
		$this->set('videos', $this->paginate());
	}

	public function admin_add() {
		if ($this->request->is('post')) {
			$this->Video->create();
			$this->Video->set('user_id', $this->Auth->user('id'));

			if ($this->Video->save($this->request->data)) {
				$this->Session->setFlash('Your new video has been added!', 'messaging/alert-success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Your new video could not be saved. Please, try again.', 'messaging/alert-error');
			}
		}
		
		//$this->set('tags', array_values($this->Video->Tag->find('list')));
	}

	public function admin_view($id = null) {
		$this->Video->id = $id;
		if (!$this->Video->exists()) {
			throw new NotFoundException(__('Invalid link'));
		}
		$video = $this->Video->find('first', array(
			'contain' => array('User'),
			'conditions' => array(
				'Video.id' => $id
			)
		));
		$this->set('video', $video);

		// $tally = $this->Message->getTally(array(
		// 	'foreign_id' => $link['Video']['id'],
		// 	'model' => 'Video'
		// ));
		// $this->set('message_tally', $tally);

		// // owner
		// if($this->Auth->user('id') == $link['Video']['user_id']) {
		// 	$this->set('tag_tally', $this->Video->getTagTally($this->Auth->user('id')));
		// } else {
		// 	$this->set('tag_tally', $this->Video->getTagTally());
		// }
		$this->set('thumbnail_path', $this->Video->thumbnailPath);
	}

}