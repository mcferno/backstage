<?php

/**
 * Helps create the initial admin user
 *
 * @property User $User
 */
class AdminCreateShell extends AppShell
{
	public $uses = array('User');

	public function main()
	{
		// can't re-add an admin account
		if(!$this->isAdminAccountRequired()) {
			$this->out('Admin user already exists, skipping ... ');
			return true;
		}

		$do = $this->in('No admin account exists, would you like to create one now?', array('y', 'n'), 'y');

		if($do !== 'y') {
			$this->out(array(
				'To add an admin user later, run the following:',
				'`./Vendor/bin/cake admin_create`'
			));

			return true;
		}

		$username = $this->in('Please provide the admin username:');
		$password = $this->in('Please provide the admin password:');
		$email = $this->in('Please provide the admin email:');

		try {
			$this->execute($username, $password, $email);
		} catch (Exception $e) {
			$this->out(array(
				'Admin user creation failed with the error(s):',
				$e->getMessage()
			));
			return false;
		}

		$this->out(array(
			'The administrator account "' . $username . '" has been created!'
		));

		return true;
	}

	/**
	 * Creates the first admin user for our system
	 *
	 * @param string $username Desired username for the admin
	 * @param string $password
	 * @param string $email Email address in case of password reset
	 * @throws Exception
	 */
	public function execute($username, $password, $email)
	{
		if($this->User->countAdminUsers()) {
			throw new Exception('Admin user already exists, another one can not be created');
		}

		$result = $this->User->createAdminAccount($username, $password, $email);

		if($result === false) {
			$fields = $this->User->invalidFields();
			$error_output = array();
			foreach($fields as $field_name => $errors) {
				if(is_array($errors)) {
					$error_output[] = current($errors);
				}
			}
			throw new Exception(implode(', ', $error_output));
		}
	}

	protected function isAdminAccountRequired()
	{
		return ! $this->User->countAdminUsers();
	}
}