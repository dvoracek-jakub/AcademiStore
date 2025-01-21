<?php declare(strict_types=1);

namespace App\Model\Category;

use App\Model\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use App\Model\Product\Product;

/**
 * @ORM\Entity(repositoryClass="App\Model\Category\CategoryRepository")
 * @ORM\Table(name="`category`")
 */
class Category extends AbstractEntity
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected int $id;

	/**
	 * @OneToOne(targetEntity="\App\Model\Category\Category")
	 * @JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
	 */
	private $parent;

	/**
	 * @ORM\Column(type="integer", name="parent_id", nullable=true, options={"default": null})
	 */
	private $parentId;

	/**
	 * @ORM\ManyToMany(targetEntity="\App\Model\Product\Product", mappedBy="categories")
	 */
	private $products;

	/**
	 * @ORM\Column(type="string")
	 */
	private $name;

	/**
	 * @ORM\Column(type="string", name="url_slug")
	 */
	private $urlSlug;

	/**
	 * @ORM\Column(type="string", name="description")
	 */
	private $description;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $active;

	public function __construct()
	{
		/* Default values */
		$this->active = 1;
		$this->parentId = null;
	}

	public function getProducts(): \Doctrine\Common\Collections\Collection
	{
		return $this->products;
	}

	/**
	 * @return self
	 */
	public function addProduct(Product $product): self
	{
		if (!$this->products->contains($product)) {
			$this->products[] = $product;
			$product->addCategory($this);
		}
		return $this;
	}

	/**
	 * @return self
	 */
	public function removeProduct(Product $product): self
	{
		if ($this->products->removeElement($product)) {
			$product->removeCategory($this);
		}
		return $this;
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
	public function getDescription(): ?string
	{
		return $this->description;
	}

	/**
	 * @param  mixed  $description
	 */
	public function setDescription($description): void
	{
		$this->description = $description;
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
	public function getParentId(): ?int
	{
		return $this->parentId;
	}

	/**
	 * @param  mixed  $parentId
	 */
	public function setParentId($parentId): void
	{
		$this->parentId = $parentId;
	}

	/**
	 * @return mixed
	 */
	public function getParent()
	{
		return $this->parent;
	}

	/**
	 * @param  mixed  $parent
	 */
	public function setParent($parent): void
	{
		$this->parent = $parent;
	}

}