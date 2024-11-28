<?php declare(strict_types=1);

namespace App\Model\Product;

use App\Model\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Model\Product\ProductRepository")
 * @ORM\Table(name="`product`")
 */
class Product extends AbstractEntity
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
	private $sku;

	/**
	 * @ORM\Column(type="string")
	 */
	private $name;

	/**
	 * @ORM\Column(type="string", name="url_slug")
	 */
	private $urlSlug;

	/**
	 * @ORM\Column(type="string", name="description_short")
	 */
	private $descShort;

	/**
	 * @ORM\Column(type="string", name="description_long")
	 */
	private $descLong;

	/**
	 * @ORM\Column(type="decimal")
	 */
	private $price;

	/**
	 * @ORM\Column(type="integer")
	 */
	private $stock;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $active;

	/**
	 * @ORM\Column(type="datetime", name="created_at")
	 */
	private $createdAt;


	/**
	 * @return mixed
	 */
	public function getSku(): string
	{
		return $this->sku;
	}

	/**
	 * @param  mixed  $sku
	 */
	public function setSku($sku): void
	{
		$this->sku = $sku;
	}

	/**
	 * @return mixed
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @param  mixed  $name
	 */
	public function setName($name): void
	{
		$this->name = $name;
	}

	/**
	 * @return mixed
	 */
	public function getUrlSlug(): string
	{
		return $this->urlSlug;
	}

	/**
	 * @param  mixed  $urlSlug
	 */
	public function setUrlSlug($urlSlug): void
	{
		$this->urlSlug = $urlSlug;
	}

	/**
	 * @return mixed
	 */
	public function getDescShort(): ?string
	{
		return $this->descShort;
	}

	/**
	 * @param  mixed  $descShort
	 */
	public function setDescShort($descShort): void
	{
		$this->descShort = $descShort;
	}

	/**
	 * @return mixed
	 */
	public function getDescLong(): ?string
	{
		return $this->descLong;
	}

	/**
	 * @param  mixed  $descLong
	 */
	public function setDescLong($descLong): void
	{
		$this->descLong = $descLong;
	}

	/**
	 * @return mixed
	 */
	public function getPrice(): float
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

	/**
	 * @return mixed
	 */
	public function getStock(): int
	{
		return $this->stock;
	}

	/**
	 * @param  mixed  $stock
	 */
	public function setStock($stock): void
	{
		$this->stock = $stock;
	}

	/**
	 * @return mixed
	 */
	public function getActive(): bool
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
	 * @return mixed
	 */
	public function getCreatedAt(): ?\DateTime
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

}