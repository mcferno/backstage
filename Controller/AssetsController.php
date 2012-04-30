<?php
/**
 * Handles all tasks related to manipulation and management a user's site assets
 */

class AssetsController extends AppController {

	public $paginate = array(
		'order' => 'created DESC',
		'limit' => 12
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
		$this->set('images',$this->paginate());
		$this->set('user_dir','user/'.$this->Auth->user('id').'/');
	}
	
	/**
	 * Saves ajax posted image data
	 */
	public function admin_save() {

		$response = array('image_saved' => false);
		
		// process upload
		if(!empty($this->request->data['image'])) {
			$status = $this->Asset->saveImage($this->request->data['image'],$this->Auth->user('id'));
			
			if($status === true) {
				$response['image_saved'] = true;
			}
		}
		
		$this->set($response);
		$this->set('_serialize', array_keys($response));
	}
	
	/**
	 * View an individual asset
	 *
	 * @param {UUID} $id Primary key of the desired asset
	 */
	public function admin_view($id = null) {
		$asset = $this->Asset->find('first',array(
			'conditions' => array(
				'Asset.id' => $id,
				'Asset.user_id'=>$this->Auth->user('id')
			)
		));
		if(empty($asset)) {
			$this->Session->setFlash('Image could not be found.','messaging/alert-error');
			$this->redirect($this->referer('index'));
		}
		$this->set('asset',$asset);	
		$this->set('user_dir','user/'.$this->Auth->user('id').'/');
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