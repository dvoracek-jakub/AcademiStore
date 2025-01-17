<?php declare(strict_types=1);

namespace App\Model\Product;

use App\Model\Product\Product;
use App\Model\Category\Category;
use Nette\Application\UI\Form;
use App\Model\Product\ProductImage;

class ProductFacade
{

	/** @var \App\Model\Product\ProductRepository|\Doctrine\ORM\EntityRepository */
	private $productRepository;
	/**
	 * @var \App\Model\Category\Category|\Doctrine\ORM\EntityRepository
	 */
	private $categoryRepository;

	public function __construct(
		public \App\Model\EntityManagerDecorator $em,
		private ProductImage $productImage
	) {
		$this->productRepository = $this->em->getRepository(Product::class);
		$this->categoryRepository = $this->em->getRepository(Category::class);
	}

	public function saveProduct($data, array $categories, int $id = null): Product
	{
		if ($id) {
			$product = $this->productRepository->findOneById($id);
			$this->productRepository->unwireCategories($id);
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

		if (!empty($categories)) {
			foreach ($categories as $catId) {
				$category = $this->categoryRepository->find($catId);
				if ($category) {
					$product->addCategory($category);
				}
			}
		}

		$this->em->persist($product);
		$this->em->flush();
		return $product;
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