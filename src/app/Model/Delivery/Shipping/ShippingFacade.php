<?php

namespace App\Model\Delivery\Shipping;

class ShippingFacade
{

	/** @var \App\Model\Delivery\Shipping\ShippingRepository|\Doctrine\ORM\EntityRepository */
	private $shippingRepository;

	public function __construct(
		private \App\Model\EntityManagerDecorator $em,
		private \App\Model\Product\ProductPrice $productPrice
	) {
		$this->shippingRepository = $this->em->getRepository(\App\Model\Delivery\Shipping\Shipping::class);
	}

	public function getShippingsArray(bool $appendPrice = false): array
	{
		$shippings = $this->em->getRepository(\App\Model\Delivery\Shipping\Shipping::class)->findBy([], ['priority' => 'ASC']);
		$out = [];
		if ($shippings) {
			foreach ($shippings as $shipping) {
				$shippingName = $shipping->getName();

				if ($appendPrice) {
					$shippingName .= ' <span class="comment">[' . $this->productPrice->format($shipping->getPrice()) . ']</span>';
				}

				$out[$shipping->getId()] = $shippingName;
			}
		}
		return $out;
	}

}