<?php
/**
 * Handles all tasks related to manipulation and management a user's site assets
 */

class AssetsController extends AppController {

	public $paginate = array(
		'order' => 'created DESC',
		'limit' => 9
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
	
	public function admin_save() {

		$response = array();
		
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
	
}