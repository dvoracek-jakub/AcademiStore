<?php

declare(strict_types=1);

namespace App\Model\Cart;

use App\Model\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use App\Model\Customer\Customer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Model\Cart\CartRepository")
 * @ORM\Table(name="`cart`")
 */
class Cart extends AbstractEntity
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", nullable=FALSE)
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected int $id;

	/**
	 * @ORM\Column(type="integer", name="customer_id")
	 */
	private int $customerId;

	/**
	 * @ORM\Column(type="string", name="session_id")
	 */
	private string $sessionId;

	/**
	 * @ORM\OneToMany(targetEntity="App\Model\Cart\CartItem", mappedBy="cart", cascade={"persist"})
	 */
	private Collection $items;

	/**
	 * @ORM\Column(type="datetime", name="created_at")
	 */
	private $createdAt;

	/**
	 * @ORM\Column(type="string")
	 */
	private $status;

	/**
	 * @ORM\ManyToOne(targetEntity="\App\Model\Customer\Customer", inversedBy="carts")
	 * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", nullable=true)
	 */
	private Customer $customer;

	public function __construct()
	{
		$this->createdAt = new \DateTime();
		$this->items = new ArrayCollection();
	}

	/**
	 * @return Collection|\App\Model\Cart\CartItem[]
	 */
	public function getItems(): Collection
	{
		return $this->items;
	}

	public function addItem(CartItem $item)
	{
		if (!$this->items->contains($item)) {
			$this->items[] = $item;
			$item->setCart($this);
		}
	}

	public function removeItem(CartItem $item)
	{
		if ($this->items->contains($item)) {
			$this->items->removeElement($item);
			if ($item->getCart() === $this) {
				$item->setCart(null);
			}
		}
	}

	public function getCustomer(): Customer
	{
		return $this->customer;
	}

	public function setCustomer(?Customer $customer)
	{
		$this->customer = $customer;
	}

	/**
	 * @return mixed
	 */
	public function getSessionId()
	{
		return $this->sessionId;
	}

	/**
	 * @param  mixed  $sessionId
	 */
	public function setSessionId($sessionId): void
	{
		$this->sessionId = $sessionId;
	}

	public function getCustomerId(): int
	{
		return $this->customerId;
	}

	public function setCustomerId(int $customerId): void
	{
		$this->customerId = $customerId;
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