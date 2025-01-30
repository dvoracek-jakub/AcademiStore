<?php

declare(strict_types=1);

namespace App\Model\Cart;

use App\Model\Product\Product;
use App\Model\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Model\Cart\CartRepository")
 * @ORM\Table(name="`cart_item`")
 */
class CartItem extends AbstractEntity
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected int $id;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Model\Cart\Cart", inversedBy="items")
	 * @ORM\JoinColumn(name="cart_id", referencedColumnName="id")
	 */
	private Cart $cart;

	/**
	 * @ORM\Column(type="integer")
	 */
	private int $quantity;

	/**
	 * @ORM\Column(type="decimal")
	 */
	private $price;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Model\Product\Product")
	 * @ORM\JoinColumn(name="product_id", referencedColumnName="id") ℹ️
	 */
	private Product $product;

	public function getCart(): Cart
	{
		return $this->cart;
	}

	public function setCart(?Cart $cart)
	{
		$this->cart = $cart;
	}

	public function getProduct(): Product
	{
		return $this->product;
	}

	public function setProduct(Product $product): void
	{
		$this->product = $product;
	}

	public function getQuantity(): int
	{
		return $this->quantity;
	}

	public function setQuantity(int $quantity): void
	{
		$this->quantity = $quantity;
	}

	/**
	 * Gets raw price without discounts applied
	 *
	 * @return mixed
	 */
	public function getPrice()
	{
		return $this->price;
	}

	/**
	 * @param  mixed  $price
	 */
	public function setPrice($price): void
	{
		$this->price = $price;
	}

}