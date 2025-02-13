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
	
	public function getBy(array $criteria, bool $single = false)
	{
		if ($single) {
			return $this->customerRepository->findOneBy($criteria);
		} else {
			return $this->customerRepository->findBy($criteria);
		}
	}

	/**
	 * Checks if customer owns an order.
	 * With possibility to condition it by order status
	 */
	public function hasOrder(int $orderId, int $customerId, ?string $status = null)
	{
		if ((int) $customerId < 1) {
			die('ERR/8HSW2');
		}
		$orders = $this->customerRepository->getOrdersRaw($customerId);

		if (!empty($orders)) {
			foreach ($orders as $order) {
				if ($order['id'] == $orderId) {
					if ($status) {
						if ($order['status'] === $status) {
							return true;
						}
					} else {
						return true;
					}
				}
			}
		}
		return false;
	}

	public function createCustomer($data)
	{
		/** @var Customer */
		$customer = new Customer();
		$customer->setEmail($data->email);
		$customer->setPassword(password_hash($data->password, PASSWORD_ARGON2ID));
		$this->em->persist($customer);
		$this->em->flush();
	}

}