<?php

class AccountsController extends AppController
{
	public $scaffold = 'admin';

	// admin-only scaffolding
	public function beforeScaffold($method)
	{
		if(!Access::hasRole('Admin')) {
			$this->redirect(array('controller' => 'users', 'action' => 'dashboard'));
		}
		$this->set('schema', $this->Account->schema());
		$this->set('types', array('Tumblr' => 'Tumblr', 'Twitter' => 'Twitter'));
		return parent::beforeScaffold($method);
	}

	public function adminBeforeRender()
	{
		parent::adminBeforeRender();
		$this->set('title', 'Accounts');
	}
}
