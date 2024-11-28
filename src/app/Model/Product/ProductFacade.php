<?php declare(strict_types=1);

namespace App\Model\Product;



use App\Model\Product\Product as ProductEntity;

class ProductFacade
{

	public function __construct(public \App\Model\EntityManagerDecorator $em)
	{
	}

	public function createProduct($data): ProductEntity
	{
		$product = new ProductEntity();
		$product->setName($data->name);
		$product->setSku($data->sku);
		$product->setPrice($data->price);
		$product->setDescShort($data->description_short);
		$product->setDescLong($data->description_long);

		$this->em->persist($product);
		$this->em->flush();

		return $product;
	}

}