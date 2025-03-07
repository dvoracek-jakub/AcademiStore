<?php

declare(strict_types=1);

namespace App\Model\Order;

use App\Model\Order\Order;
use App\Model\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Model\Order\OrderRepository")
 * @ORM\Table(name="`order_delivery_data`")
 */
class OrderDeliveryData
{

	/**
	 * @ORM\Id
	 * @ORM\OneToOne(targetEntity="App\Model\Order\Order", inversedBy="orderDeliveryData")
	 * @ORM\JoinColumn(name="order_id", referencedColumnName="id", nullable=false)
	 */
	protected Order $order;

	/**
	 * @ORM\Column(type="string")
	 */
	private $firstname;

	/**
	 * @ORM\Column(type="string")
	 */
	private $lastname;

	/**
	 * @ORM\Column(type="string")
	 */
	private $email;

	/**
	 * @ORM\Column(type="string")
	 */
	private $phone;

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
	 * @return mixed
	 */
	public function getFirstname()
	{
		return $this->firstname;
	}

	/**
	 * @param  mixed  $firstname
	 */
	public function setFirstname($firstname): void
	{
		$this->firstname = $firstname;
	}

	/**
	 * @return mixed
	 */
	public function getLastname()
	{
		return $this->lastname;
	}

	/**
	 * @param  mixed  $lastname
	 */
	public function setLastname($lastname): void
	{
		$this->lastname = $lastname;
	}

	/**
	 * @return mixed
	 */
	public function getPhone()
	{
		return $this->phone;
	}

	/**
	 * @param  mixed  $phone
	 */
	public function setPhone($phone): void
	{
		$this->phone = $phone;
	}

	/**
	 * @return mixed
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * @param  mixed  $email
	 */
	public function setEmail(string $email): void
	{
		$this->email = $email;
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

	public function getOrder(): \App\Model\Order\Order
	{
		return $this->order;
	}

	public function setOrder(\App\Model\Order\Order $order): void
	{
		$this->order = $order;
	}

}