<?php declare(strict_types=1);

namespace App\Model\Product;

use App\Model\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use App\Model\Product\Product;

/**
 * @ORM\Entity(repositoryClass="App\Model\Product\ProductRepository")
 * @ORM\Table(name="`product_discount`")
 */
class ProductDiscount extends AbstractEntity
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected int $id;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Model\Product\Product", inversedBy="discounts")
	 * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
	 */
	protected Product $product;

	/**
	 * @ORM\Column(type="decimal")
	 */
	private $price;

	/**
	 * @ORM\Column(type="datetime", name="start_date")
	 */
	private $startDate;

	/**
	 * @ORM\Column(type="datetime", name="end_date")
	 */
	private $endDate;

	/**
	 * @ORM\Column(type="integer", name="from_quantity")
	 */
	private $fromQuantity;

	public function getProduct(): Product
	{
		return $this->product;
	}

	public function setProduct(?Product $product)
	{
		$this->product = $product;
	}

	/**
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

	/**
	 * @return mixed
	 */
	public function getEndDate()
	{
		return $this->endDate;
	}

	/**
	 * @param  mixed  $endDate
	 */
	public function setEndDate($endDate): void
	{
		$this->endDate = \DateTime::createFromFormat('Y-m-d', $endDate);
	}

	/**
	 * @return mixed
	 */
	public function getStartDate()
	{
		return $this->startDate;
	}

	/**
	 * @param  mixed  $startDate
	 */
	public function setStartDate($startDate): void
	{
		$this->startDate = \DateTime::createFromFormat('Y-m-d', $startDate);
	}

	/**
	 * @return mixed
	 */
	public function getFromQuantity()
	{
		return $this->fromQuantity;
	}

	/**
	 * @param  mixed  $fromQuantity
	 */
	public function setFromQuantity($fromQuantity): void
	{
		$this->fromQuantity = $fromQuantity;
	}

}
