<?php

namespace App\Module\Front\Accessory\Form;

use Nette\Application\UI\Form;
use App\Model\Shipping\ShippingFacade;
use App\Model\Payment\PaymentFacade;

class CheckoutFormFactory
{

	public function __construct(
		public ShippingFacade $shippingFacade,
		public PaymentFacade $paymentFacade,
		private \App\Core\Settings $settings,
	) {}


	public function createCheckoutForm(): Form
	{
		$form = new Form();
		$shippings = $this->shippingFacade->getShippingsArray(true);
		$payments = $this->paymentFacade->getPaymentsArray(true);
		$form->addRadioList('shippingId', 'Doprava', $shippings);
		$form->addRadioList('paymentId', 'Platba', $payments);

		$form->addSubmit('submit', 'Odeslat objednávku');
		$form->onSuccess[] = [$this, 'formSucceeded'];
		return $form;
	}

	public function formSucceeded(Form $form, $data)
	{
		// $form->addError('Zadané SKU již existuje');
		bdump($data);

		if (!$this->shippingFacade->hasValidPayment($data->shippingId, $data->paymentId)) {
			$form->addError('Zvolená platební metoda není pro tuto dopravu k dispozici');
		}

		if (!$form->hasErrors()) {
			//$this->productFacade->saveProduct($data, $categories, $discounts, $this->id);
		}
	}
}
