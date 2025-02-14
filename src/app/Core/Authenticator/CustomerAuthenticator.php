<?php

namespace App\Core\Authenticator;

use Nette;
use App\Model\Customer\Customer;
use Nette\Security\SimpleIdentity;

class CustomerAuthenticator
{

	public function __construct(private \App\Model\Customer\CustomerService $customerService) {}

	public function authenticate(string $email, string $password): ?SimpleIdentity
	{
		/** @var Customer */
		$customer = $this->customerService->getBy(['email' => $email], true);
		if ($customer && password_verify($password, $customer->getPassword())) {
			return new SimpleIdentity(
				$customer->getId(),
				['customer'],
				[
					'firstname'  => $customer->getFirstname(),
					'lastname'  => $customer->getLastname(),
					'phone'  => $customer->getPhone(),
					'email' => $customer->getEmail(),
				],
			);
		}
		throw new \Nette\Security\AuthenticationException('The username or password you entered is incorrect.');
	}

}
