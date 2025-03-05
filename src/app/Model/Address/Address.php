<?php

declare(strict_types=1);

namespace App\Model\Address;

use App\Model\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use App\Model\Customer\Customer;


/**
 * @ORM\Entity(repositoryClass="App\Model\Customer\CustomerRepository")
 * @ORM\Table(name="`address`")
 */
class Address extends AbstractEntity
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected int $id;

	/**
	 * @ORM\OneToOne(targetEntity="App\Model\Customer\Customer", inversedBy="address")
	 * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", nullable=false)
	 */
	private Customer $customer;

	/**
	 * @ORM\Column(type="string")
	 */
	private $street;

	/**
	 * @ORM\Column(type="string")
	 */
	private $city;

	/**
	 * @ORM\Column(type="string")
	 */
	private $zip;

	/**
	 * Get the id of the Address.
	 */
	public function getId(): int
	{
		return $this->id;
	}

	/**
	 * @return mixed
	 */
	public function getStreet()
	{
		return $this->street;
	}

	/**
	 * @param  mixed  $street
	 */
	public function setStreet($street): void
	{
		$this->street = $street;
	}

	/**
	 * @return mixed
	 */
	public function getCity()
	{
		return $this->city;
	}

	/**
	 * @param  mixed  $city
	 */
	public function setCity($city): void
	{
		$this->city = $city;
	}

	/**
	 * @return mixed
	 */
	public function getZip()
	{
		return $this->zip;
	}

	/**
	 * @param  mixed  $zip
	 */
	public function setZip($zip): void
	{
		$this->zip = $zip;
	}

	/**
	 * @return \App\Model\Customer\Customer
	 */
	public function getCustomer(): Customer
	{
		return $this->customer;
	}

	/**
	 * @param  \App\Model\Customer\Customer  $customer
	 */
	public function setCustomer(Customer $customer): void
	{
		$this->customer = $customer;
	}

}