<?php declare(strict_types=1);

namespace App\Model\Product;

use App\Model\Product\Product;
use App\Model\Category\Category;
use Nette\Application\UI\Form;
use App\Model\Product\ProductImage;
use App\Model\Product\ProductDiscount;

class ProductService
{

	/** @var \App\Model\Product\ProductRepository|\Doctrine\ORM\EntityRepository */
	private $productRepository;
	/**
	 * @var \App\Model\Category\Category|\Doctrine\ORM\EntityRepository
	 */
	private $categoryRepository;

	public function __construct(
		public \App\Model\EntityManagerDecorator $em,
		private ProductImage $productImage,
		private \App\Model\Product\ProductPrice $productPrice
	) {
		$this->productRepository = $this->em->getRepository(Product::class);
		$this->categoryRepository = $this->em->getRepository(Category::class);
		$this->productPrice = $productPrice;
	}

	public function saveProduct($data, array $categories, array $discounts, int $id = null): Product
	{
		if ($id) {
			$product = $this->productRepository->findOneById($id);
			$this->productRepository->unwireCategories($id);
			$this->productRepository->removeDiscounts($id);
			$product->getCategories()->clear();
			$product->getDiscounts()->clear();
		} else {
			$product = new Product();
		}

		$product->setName($data->name);
		$product->setPrice($data->price);
		$product->setDescShort($data->descShort);
		$product->setDescLong($data->descLong);
		$product->setActive($data->active);
		$product->setUrlSlug($this->generateUrlSlug($data->name));

		if (!empty($data->imageName) || $data->deleteImage) {
			$product->setImageName($data->imageName);
		}

		if (empty($data->sku)) {
			$data->sku = $this->generateSku($data->name);
		}
		$product->setSku($data->sku);

		// Relate categories
		if (!empty($categories)) {
			foreach ($categories as $catId) {
				$category = $this->categoryRepository->find($catId);
				if ($category) {
					$product->addCategory($category);
				}
			}
		}

		// Relate discounts
		if (!empty($discounts)) {
			foreach ($discounts as $discount) {
				$productDiscount = new ProductDiscount();
				$productDiscount->setPrice($discount['price']);

				if (!empty($discount['start_date'])) {
					$productDiscount->setStartDate($discount['start_date']);
				}
				if (!empty($discount['end_date'])) {
					$productDiscount->setEndDate($discount['end_date']);
				}
				if (!empty($discount['quantity'])) {
					$productDiscount->setFromQuantity($discount['quantity']);
				}
				$product->addDiscount($productDiscount);
			}
		}

		$this->em->persist($product);
		$this->em->flush();
		$this->setLowestPrice($product->getId());
		return $product;
	}

	/**
	 * Sets the lowest price for a given product by updating its lowest unit price based on active pricing rules.
	 * TODO Call for each product in a CRON task every day
	 */
	public function setLowestPrice(int $productId)
	{
		$product = $this->productRepository->findOneById($productId);
		$this->productPrice->setProduct($product);
		$lowestPrice = $this->productPrice->getLowestActivePrice(1);
		$product->setLowestUnitPrice($lowestPrice);
		$this->em->persist($product);
		$this->em->flush();
		bdump($lowestPrice, 'Setting lowest active price');
	}

	public function generateUrlSlug(string $name): string
	{
		$urlSlug = \Nette\Utils\Strings::webalize($name);

		// Check if the slug already exists. If so, modify it a bit.
		$product = $this->productRepository->findBy(['urlSlug' => $urlSlug]);
		if (isset($product[0])) {
			$urlSlug = $this->generateUrlSlug($name . '-' . rand(1, 999));
		}
		return $urlSlug;
	}

	public function getProducts(Category $category, int $offset, int $length, array $filter, bool $countOnly = false)
	{
		$order = 'p.name ASC';
		if (isset($filter['order'])) {
			if ($filter['order'] == 'price_asc') {
				$order = 'p.lowestUnitPrice ASC';
			}
			if ($filter['order'] == 'price_desc') {
				$order = 'p.lowestUnitPrice DESC';
			}
			if ($filter['order'] == 'name_asc') {
				$order = 'p.name ASC';
			}
			if ($filter['order'] == 'name_desc') {
				$order = 'p.name DESC';
			}
		}

		$where = [];
		if (isset($filter['priceFrom']) && (int) $filter['priceFrom'] > 0) {
			$where['p.lowestUnitPrice >= '] = (int) $filter['priceFrom'];
		}
		if (isset($filter['priceTo']) && (int) $filter['priceTo'] > 0) {
			$where['p.lowestUnitPrice <= '] = (int) $filter['priceTo'];
		}

		return $this->productRepository->getProducts($category, $offset, $length, $where, $order, $countOnly);
	}

	public function generateSku(string $name, array $variants = []): string
	{
		// Normalize symbols
		$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $name);
		$clean = preg_replace('/[^a-zA-Z0-9]+/', '-', $clean);

		// First-letterize & append random number
		$parts = array_map(function($word) {
			return strtoupper(substr($word, 0, 3));
		}, explode('-', $clean));
		$sku = implode('-', array_slice($parts, 0, 3)) . '-' . rand(100, 999);

		// Possible variants
		if (!empty($variants)) {
			foreach ($variants as $key => $value) {
				$sku .= '-' . strtoupper(substr($key, 0, 1) . substr($value, 0, 2));
			}
		}
		return $sku;
	}

	public function skuExists(string $sku): bool
	{
		$product = $this->productRepository->findBy(['sku' => $sku]);
		return isset($product[0]);
	}

	public function deleteImage(int $id)
	{
		$product = $this->productRepository->findOneById($id);

		if (!empty($product->getImageName())) {
			$this->productImage->deleteImage($product->getImageName());
		}
	}

}