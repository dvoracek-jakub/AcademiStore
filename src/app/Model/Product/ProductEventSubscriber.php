<?php

namespace App\Model\Product;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Events;
use App\Model\Product\ProductImage;

class ProductEventSubscriber implements EventSubscriber
{
	/** @var ProductImage  */
	private ProductImage $productImage;

	public function __construct(ProductImage $productImage)
	{
		$this->productImage = $productImage;
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
		}
	}
}