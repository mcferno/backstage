<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 */
class PagesController extends AppController {

/**
 * Controller name
 *
 * @var string
 */
	public $name = 'Pages';

/**
 * Default helper
 *
 * @var array
 */
	public $helpers = array('Html');

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array('Tumblr', 'Contest', 'Asset');

/**
 * Displays a view
 *
 * @param mixed What page to display
 */
	public function display() {				
		$path = func_get_args();

		$count = count($path);
		if (!$count) {
			$this->redirect('/');
		}
		$page = $subpage = $title_for_layout = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}
		$this->set(compact('page', 'subpage', 'title_for_layout'));
		$this->render(implode('/', $path));
	}
	
	public function quote_generator() {
		$quote = ClassRegistry::init('Quote')->generate();
		$this->set(compact('quote'));
		
		if($this->request->is('ajax')) {
			$this->disableCache(); // expire cache immediately
			$this->RequestHandler->renderAs($this, 'json');
			$this->set('_serialize', array('quote'));
		}
	}
	
	/**
	 * Presents the interface for the js-driven meme generator tool.
	 */
	public function admin_meme_generator() {

		// specific subset of images specified
		if(!empty($this->request->pass[0])) {
			$images = glob(IMAGES.'user'.DS.$this->request->pass[0].'*');
			$images = array_merge($images, glob(IMAGES.'base-meme'.DS.$this->request->pass[0].'*'));
		
		// meme of a specific user-uploaded image
		} elseif(!empty($this->request->params['named']['asset'])) {
			$path = $this->Asset->getPath($this->request->params['named']['asset']);
			
			if(!empty($path)) {
				$images[] = $path;
			}

		// caption contest
		} elseif(!empty($this->request->params['named']['contest'])) {
			$contest = $this->Contest->getActiveContest($this->request->params['named']['contest']);

			if($contest === false) {
				$this->Session->setFlash('Sorry, the contest is either done, or could not be found.', 'messaging/alert-error');
				$this->redirect(array('controller' => 'contests', 'action' => 'index'));
			}

			$images[] = $this->Asset->getPath($contest['Asset']['id']);

			$this->set('contest', $contest);
		}
		
		// fallback set of images
		if(empty($images)) {
			$images = glob(IMAGES.'base-meme'.DS.'*.*');
			foreach ($images as &$image) {
				$image = substr($image,strlen(IMAGES));
			}
		}

		$this->set('base_images',$images);
	}
	
	/**
	 * Clears the view cache.
	 */
	public function admin_clear_cache() {
		if($this->Auth->user('role') >= ROLES_ADMIN) {
			if(clearCache() === true) {
				$msg = 'View cache has been cleared successfully!';
				$type = 'messaging/alert-success';
			} else {
				$msg = 'View cache could not be cleared!';
				$type = 'messaging/alert-error';
			}
			$this->Session->setFlash($msg,$type);
		}
		$this->redirect($this->referer(array('controller'=>'users','action'=>'dashboard')));
	}
}
