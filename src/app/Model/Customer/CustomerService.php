<?php

declare(strict_types=1);

namespace App\Model\Customer;

use App\Model\Address\Address;
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

	public function getBy(array $criteria = [], bool $single = false)
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

	public function saveCustomer($data, ?int $id = null)
	{
		if ($id) {
			$customer = $this->customerRepository->find($id);
		} else {
			/** @var Customer */
			$customer = new Customer();
		}

		if (isset($data->firstname) && !empty($data->firstname)) {
			$customer->setFirstname($data->firstname);
		}
		
		if (isset($data->email) && !empty($data->email)) {
			$customer->setEmail($data->email);
		}

		if (isset($data->password) && !empty($data->password)) {
			$customer->setPassword(password_hash($data->password, PASSWORD_ARGON2ID));
		}

		$this->em->persist($customer);
		$this->em->flush();
	}

	public function saveAddress($data, int $customerId)
	{
		$address = $this->em->getRepository(Address::class)->findOneBy(['customer' => $customerId]);
		if (!$address instanceof Address) {
			$address = new Address();
		}

		$customer = $this->customerRepository->find($customerId);
		$address->setCustomer($customer);
		$address->setCity($data->city);
		$address->setStreet($data->street);
		$address->setZip($data->zip);
		$this->em->persist($address);
		$this->em->flush();
	}

}