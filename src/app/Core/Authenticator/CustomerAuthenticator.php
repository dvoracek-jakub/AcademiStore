<?php

namespace App\Core\Authenticator;

use Nette;
use Nette\Security\SimpleIdentity;

class CustomerAuthenticator
{

	public function authenticate(string $username, string $password): ?SimpleIdentity
	{
		$customerExists = true; // @TODO Zde bude overeni oproti DB
		if ($customerExists) {
			return new SimpleIdentity(
				2,  //  ID
				'customer', // role (nebo pole s rolemi)
				['name' => 'Ciryl'],
			);
		}
		throw new \Nette\Security\AuthenticationException('The username or password you entered is incorrect.');
	}

}
