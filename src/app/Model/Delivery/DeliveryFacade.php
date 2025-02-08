<?php

declare(strict_types=1);

namespace App\Model\Delivery;

use App\Model\Delivery\Payment\Payment;
use App\Model\Delivery\Shipping\Shipping;

class DeliveryFacade
{

	public function __construct(
		private \Nette\Http\Session $session,
		private \App\Model\EntityManagerDecorator $em,
	) {}

	public function recalculateTotals($cartFacade)
	{
		$cart = $cartFacade->getCurrentCart();
		$checkoutSession = $this->session->getSection('deliveryOptions');
		$totals = [
			'withoutTax' => 0,
			'withTax'    => 0,
		];

		// Add items cost
		$cartTotals = $cartFacade->getCartTotals($cart);
		$totals['withoutTax'] += $cartTotals['withoutTax'];
		$totals['withTax'] += $cartTotals['withTax'];

		// Add shipping cost
		if ($checkoutSession->shippingId > 0) {
			$shipping = $this->em->getRepository(Shipping::class)->find($checkoutSession->shippingId);
			$totals['withoutTax'] += $shipping->getPrice() / 1.2; // todo later lépěji
			$totals['withTax'] += $shipping->getPrice();
		}

		// Add payment cost
		if ($checkoutSession->paymentId > 0) {
			$payment = $this->em->getRepository(Payment::class)->find($checkoutSession->paymentId);
			$totals['withoutTax'] += $payment->getPrice() / 1.2;  // todo later lépěji
			$totals['withTax'] += $payment->getPrice();
		}
		return $totals;
	}

	public function isValidDeliveryCombination(int $shippingId, int $paymentId): bool
	{
	    return true; // todo later
	}

}