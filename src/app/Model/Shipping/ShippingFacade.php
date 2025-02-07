<?php

namespace App\Model\Shipping;

use App\Model\Shipping\Shipping;

class ShippingFacade
{

	/** @var \App\Model\Shipping\ShippingRepository|\Doctrine\ORM\EntityRepository */
	private $shippingRepository;

	public function __construct(
		private \App\Model\EntityManagerDecorator $em,
		private \App\Model\Product\ProductPrice $productPrice
	)
	{
		$this->shippingRepository = $this->em->getRepository(Shipping::class);
	}

	public function getShippingsArray(bool $appendPrice = false): array
	{
		$shippings = $this->em->getRepository(\App\Model\Shipping\Shipping::class)->findBy([], ['priority' => 'ASC']);
		$out = [];
		if ($shippings) {
			foreach ($shippings as $shipping) {
				$shippingName = $shipping->getName();

				if ($appendPrice) {
					$shippingName .=  ' <span class="comment">[' . $this->productPrice->format($shipping->getPrice()) . ']</span>';
				}

				$out[$shipping->getId()] = $shippingName;
			}
		}
		return $out;
	}

	public function hasValidPayment(int $shippingId, int $paymentId): bool
	{
	    // todo ziskavat z tabulky shipping_payment z shippingRepos...
		return false;
	}

}