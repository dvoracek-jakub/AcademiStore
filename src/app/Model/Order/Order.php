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
	private $shippingType;

	/**
	 * @ORM\ManyToOne(targetEntity="\App\Model\Delivery\Payment\Payment")
	 * @ORM\JoinColumn(name="payment_id", referencedColumnName="id")
	 */
	private $paymentType;

	/**
	 * @ORM\OneToOne(targetEntity="\App\Model\Order\OrderPayment", mappedBy="order")
	 */
	public $paymentStatus;

	/**
	 * @ORM\Column(type="datetime", name="created_at")
	 */
	private $createdAt;

	/**
	 * @ORM\Column(type="string")
	 */
	private $status;

	public function __construct()
	{
		$this->createdAt = new \DateTime();
		$this->status = 'NEW';
	}

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
	public function getShippingType()
	{
		return $this->shippingType;
	}

	/**
	 * @param  mixed  $shippingType
	 */
	public function setShippingType($shippingType): void
	{
		$this->shippingType = $shippingType;
	}

	/**
	 * @return mixed
	 */
	public function getPaymentType()
	{
		return $this->paymentType;
	}

	/**
	 * @param  mixed  $paymentType
	 */
	public function setPaymentType($paymentType): void
	{
		$this->paymentType = $paymentType;
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

	/**
	 * @return mixed
	 */
	public function getPaymentStatus()
	{
		return $this->paymentStatus;
	}

	/**
	 * @param  mixed  $paymentStatus
	 */
	public function setPaymentStatus($paymentStatus): void
	{
		$this->paymentStatus = $paymentStatus;
	}

}