<?php

namespace App\UI\Admin\Accesorry;

use Nette;
use Nette\Security\SimpleIdentity;

class AdminAuthenticator implements Nette\Security\Authenticator
{

	public function authenticate(string $username, string $password): SimpleIdentity
	{
		return new SimpleIdentity(
			2,  //id
			'admin', // role (nebo pole s rolemi)
			['name' => 'Arnošt'],
		);
	}

}
