<?php

namespace App\Core\Authenticator;

use Nette;
use Nette\Security\SimpleIdentity;

class AdminAuthenticator
{

	public function authenticate(string $username, string $password): ?SimpleIdentity
	{
		// @TODO Zde bude overeni oproti DB

		if (true) {
			return new SimpleIdentity(
				2,  //id
				'admin', // role (nebo pole s rolemi)
				['name' => 'Arnošt'],
			);
		}
		throw new \Nette\Security\AuthenticationException('The username or password you entered is incorrect.');
	}

}
