<?php

declare(strict_types=1);

namespace App\Model\Customer;

use App\Model\Order\Order;
use App\Model\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Model\Customer\CustomerRepository")
 * @ORM\Table(name="`customer`")
 */
class Customer extends AbstractEntity
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected int $id;

	/**
	 * @ORM\Column(type="string")
	 */
	private $email;

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
	private $phone;

	/**
	 * @ORM\Column(type="string")
	 */
	private $password;

	/**
	 * @ORM\Column(type="datetime", name="created_at")
	 */
	private $createdAt;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $active;

	/**
	 * @ORM\OneToMany(targetEntity="App\Model\Order\Order", mappedBy="customer", cascade={"persist"})
	 */
	private $orders;

	/**
	 * @ORM\OneToMany(targetEntity="App\Model\Cart\Cart", mappedBy="customer", cascade={"persist"})
	 */
	private Collection $carts;

	public function __construct()
	{
		$this->orders = new ArrayCollection();
		$this->createdAt = new \DateTime();
		$this->active = 1;
	}

	public function addOrder(Order $order)
	{
		if (!$this->orders->contains($order)) {
			$this->orders[] = $order;
			$order->setCustomer($this);
		}
	}

	public function removeOrder(Order $order)
	{
		if ($this->orders->contains($order)) {
			$this->orders->removeElement($order);
			if ($order->getCustomer() === $this) {
				$order->setCustomer(null);
			}
		}
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
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * @param  mixed  $password
	 */
	public function setPassword(string $password): void
	{
		$this->password = $password;
	}

	/**
	 * @return mixed
	 */
	public function getCreatedAt()
	{
		return $this->createdAt;
	}

	/**
	 * @param  mixed  $createdAt
	 */
	public function setCreatedAt($createdAt): void
	{
		$this->createdAt = $createdAt;
	}

	/**
	 * @return mixed
	 */
	public function getActive()
	{
		return $this->active;
	}

	/**
	 * @param  mixed  $active
	 */
	public function setActive($active): void
	{
		$this->active = $active;
	}

	/**
	 * @return Collection|Cart[]
	 */
	public function getCarts(): Collection
	{
		return $this->carts;
	}

	public function createCart($cart)
	{
		if (!$this->carts->contains($cart)) {
			$this->carts[] = $cart;
			$cart->setCustomer($this);
		}
	}

	public function removeCart($cart)
	{
		if ($this->carts->contains($cart)) {
			$this->carts->removeElement($cart);
			if ($cart->getCustomer() === $this) {
				$cart->setCustomer(null);
			}
		}
	}

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
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getOrders()
	{
		return $this->orders;
	}

	/**
	 * @param  \Doctrine\Common\Collections\ArrayCollection  $orders
	 */
	public function setOrders(ArrayCollection $orders): void
	{
		$this->orders = $orders;
	}

}