<?php

declare(strict_types=1);

namespace App\Model\Customer;

use App\Model\Customer\Customer;

class CustomerService
{

	/** @var \App\Model\Customer\CustomerRepository|\Doctrine\ORM\EntityRepository */
	private $customerRepository;

	public function __construct(private \App\Model\EntityManagerDecorator $em)
	{
		$this->customerRepository = $this->em->getRepository(Customer::class);
	}

	public function getCustomer(int $id): ?Customer
	{
		return $this->customerRepository->find($id);
	}

}