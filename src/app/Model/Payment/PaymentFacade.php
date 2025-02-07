<?php

namespace App\Model\Payment;

use App\Model\Payment\Payment;

class PaymentFacade
{

	/** @var \App\Model\Payment\PaymentRepository|\Doctrine\ORM\EntityRepository */
	private $paymentRepository;

	public function __construct(
		private \App\Model\EntityManagerDecorator $em,
		private \App\Model\Product\ProductPrice $productPrice
	)
	{
		$this->paymentRepository = $this->em->getRepository(Payment::class);
	}

	public function getPaymentsArray(bool $appendPrice = false): array
	{
		$payments = $this->em->getRepository(\App\Model\Payment\Payment::class)->findBy([], ['priority' => 'ASC']);
		$out = [];
		if ($payments) {
			foreach ($payments as $payment) {
				$paymentName = $payment->getName();
				if ($appendPrice) {
					$paymentName .=  ' <span class="comment">[' . $this->productPrice->format($payment->getPrice()) . ']</span>';
				}
				$out[$payment->getId()] = $paymentName;
			}
		}
		return $out;
	}

}