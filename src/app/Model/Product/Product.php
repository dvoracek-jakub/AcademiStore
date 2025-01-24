<?php declare(strict_types=1);

namespace App\Model\Product;

use App\Model\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Model\Product\ProductImage;
use App\Model\Product\ProductPrice;

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
	 * @ORM\ManyToMany(targetEntity="\App\Model\Category\Category", inversedBy="products")
	 * @ORM\JoinTable(name="product_category")
	 */
	private $categories;

	/**
	 * @ORM\OneToMany(targetEntity="App\Model\Product\ProductDiscount", mappedBy="product", cascade={"persist"})
	 */
	private Collection $discounts;

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
	 * @ORM\Column(type="string", name="image_name")
	 */
	private $imageName;

	/**
	 * @ORM\Column(type="datetime", name="created_at")
	 */
	private $createdAt;

	/** @var ProductImage */
	private $productImage;

	/** @var ProductPrice */
	private $productPrice;

	public function __construct()
	{
		/* Default values */
		$this->active = 1;
		$this->stock = 0;
		$this->createdAt = new \DateTime();
		$this->imageName = '';
		$this->categories = new ArrayCollection();
		$this->discounts = new ArrayCollection();
	}

	/**
	 * Called by \App\Model\Product\ProductEventSubscriber
	 */
	public function setProductImage(ProductImage $productImage)
	{
		$this->productImage = $productImage;
	}

	/**
	 * Called by \App\Model\Product\ProductEventSubscriber
	 */
	public function setProductPrice(ProductPrice $productPrice)
	{
		$this->productPrice = $productPrice;
		$this->productPrice->setProduct($this);
	}

	public function getImage(string $dimensions = '')
	{
		return $this->productImage->getImage($this, $dimensions);
	}

	public function getPriceHtml(bool $full = false, string $size = 'xl'): string
	{
		$priceOriginal = $this->getPrice();
		$priceWithDiscounts = $this->productPrice->getPriceWithDiscounts();

		// @ TODO zakomponuj $full = false. tedy nezobrazovat original price
		$priceClass = 'text-' . $size;
		$discountHtml = '';
		if ($priceWithDiscounts < $priceOriginal) {
			$priceClass = 'line-through text-sm';
			$discountHtml = '<span class="price-discounted ml-4 text-' . $size . '">' . $this->productPrice->format($priceWithDiscounts) . '</span>';
		}
		$html = '<span class="' . $priceClass . ' price-original">' . $this->productPrice->format($priceOriginal) . '</span>';
		$html .= $discountHtml;

		return $html;
	}

	public function getCategories(): \Doctrine\Common\Collections\Collection
	{
		return $this->categories;
	}

	public function addCategory(\App\Model\Category\Category $category)
	{
		if (!$this->categories || !$this->categories->contains($category)) {
			$this->categories[] = $category;
			$category->addProduct($this);
		}
	}

	public function removeCategory(Category $category)
	{
		if ($this->categories->removeElement($category)) {
			$category->removeProduct($this);
		}
	}

	/**
	 * @return Collection|ProductDiscount[]
	 */
	public function getDiscounts(): Collection
	{
		return $this->discounts;
	}

	public function addDiscount(ProductDiscount $discount)
	{
		if (!$this->discounts->contains($discount)) {
			$this->discounts[] = $discount;
			$discount->setProduct($this);
		}
	}

	public function removeDiscount(ProductDiscount $discount)
	{
		if ($this->discounts->contains($discount)) {
			$this->discounts->removeElement($discount);
			if ($discount->getProduct() === $this) {
				$discount->setProduct(null);
			}
		}
	}

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

	/**
	 * @return mixed
	 */
	public function getImageName(): ?string
	{
		return $this->imageName;
	}

	/**
	 * @param  mixed  $imageName
	 */
	public function setImageName($imageName): void
	{
		$this->imageName = $imageName;
	}

}