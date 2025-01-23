<?php

namespace App\Model\Product;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Events;
use App\Model\Product\ProductImage;
use App\Model\Product\ProductPrice;

class ProductEventSubscriber implements EventSubscriber
{
	/** @var ProductImage  */
	private ProductImage $productImage;

	/** @var ProductPrice  */
	private ProductPrice $productPrice;


	public function __construct(ProductImage $productImage, ProductPrice $productPrice)
	{
		$this->productImage = $productImage;
		$this->productPrice = $productPrice;
	}

	public function getSubscribedEvents(): array
	{
		return [Events::postLoad];
	}

	public function postLoad(PostLoadEventArgs $args): void
	{
		$entity = $args->getObject();
		if ($entity instanceof \App\Model\Product\Product) {
			$entity->setProductImage($this->productImage);
			$entity->setProductPrice($this->productPrice);
		}
	}
}