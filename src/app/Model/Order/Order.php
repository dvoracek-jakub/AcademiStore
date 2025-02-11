<?php

declare(strict_types=1);

namespace App\Model\Order;

use App\Model\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Model\Order\OrderRepository")
 * @ORM\Table(name="`order`")
 */
class Order extends AbstractEntity
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected int $id;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Model\Customer\Customer", inversedBy="orders")
	 * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
	 */
	private $customer;

	/**
	 * @ORM\OneToOne(targetEntity="App\Model\Cart\Cart", mappedBy="order")
	 * @ORM\JoinColumn(name="cart_id", referencedColumnName="id")
	 */
	private $cart;

	/**
	 * @ORM\ManyToOne(targetEntity="\App\Model\Delivery\Shipping\Shipping")
	 * @ORM\JoinColumn(name="shipping_id", referencedColumnName="id")
	 */
	private $shipping;

	/**
	 * @ORM\ManyToOne(targetEntity="\App\Model\Delivery\Payment\Payment")
	 * @ORM\JoinColumn(name="payment_id", referencedColumnName="id")
	 */
	private $payment;

	/**
	 * @ORM\Column(type="string", name="delivery_address")
	 */
	private $deliveryAddress;

	/**
	 * @ORM\Column(type="datetime", name="created_at")
	 */
	private $createdAt;

	/**
	 * @ORM\Column(type="string")
	 */
	private $status;

	/**
	 * @return mixed
	 */
	public function getCustomer()
	{
		return $this->customer;
	}

	/**
	 * @param  mixed  $customer
	 */
	public function setCustomer($customer): void
	{
		$this->customer = $customer;
	}

	/**
	 * @return mixed
	 */
	public function getCart()
	{
		return $this->cart;
	}

	/**
	 * @param  mixed  $cart
	 */
	public function setCart($cart): void
	{
		$this->cart = $cart;
	}

	/**
	 * @return mixed
	 */
	public function getShipping()
	{
		return $this->shipping;
	}

	/**
	 * @param  mixed  $shipping
	 */
	public function setShipping($shipping): void
	{
		$this->shipping = $shipping;
	}

	/**
	 * @return mixed
	 */
	public function getPayment()
	{
		return $this->payment;
	}

	/**
	 * @param  mixed  $payment
	 */
	public function setPayment($payment): void
	{
		$this->payment = $payment;
	}

	/**
	 * @return mixed
	 */
	public function getDeliveryAddress()
	{
		return $this->deliveryAddress;
	}

	/**
	 * @param  mixed  $deliveryAddress
	 */
	public function setDeliveryAddress($deliveryAddress): void
	{
		$this->deliveryAddress = $deliveryAddress;
	}

	/**
	 * @return mixed
	 */
	public function getCreated()
	{
		return $this->created;
	}

	/**
	 * @param  mixed  $created
	 */
	public function setCreated($created): void
	{
		$this->created = $created;
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
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @param  mixed  $status
	 */
	public function setStatus($status): void
	{
		$this->status = $status;
	}

}