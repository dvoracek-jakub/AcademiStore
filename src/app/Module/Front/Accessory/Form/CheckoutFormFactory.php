<?php

namespace App\Module\Front\Accessory\Form;

use App\Model\Delivery\DeliveryService;
use App\Model\Delivery\Payment\PaymentService;
use App\Model\Delivery\Shipping\ShippingService;
use App\Model\Order\NewOrderFacade;
use Nette\Application\UI\Form;

class CheckoutFormFactory
{

	public function __construct(
		private DeliveryService $deliveryService,
		private ShippingService $shippingService,
		private PaymentService $paymentService,
		private NewOrderFacade $newOrderFacade,
		private \App\Core\Settings $settings,
		private \Nette\Http\Session $session
	) {}


	public function createCheckoutForm(): Form
	{
		$form = new Form();
		$shippings = $this->shippingService->getShippingsArray(true);
		$payments = $this->paymentService->getPaymentsArray(true);
		$form->addRadioList('shippingId', 'Doprava', $shippings);
		$form->addRadioList('paymentId', 'Platba', $payments);

		$checkout = $this->session->getSection('deliveryOptions');
		if ($checkout->shippingId > 0) {
			$form['shippingId']->setDefaultValue($checkout->shippingId);
		}
		if ($checkout->paymentId > 0) {
			$form['paymentId']->setDefaultValue($checkout->paymentId);
		}

		$form->addSubmit('submit', 'Odeslat objednávku');
		$form->onSuccess[] = [$this, 'formSucceeded'];
		return $form;
	}

	public function formSucceeded(Form $form, $data)
	{
		// $form->addError('Zadané SKU již existuje');
		bdump($data);

		if (!$this->deliveryService->isValidDeliveryCombination($data->shippingId, $data->paymentId)) {
			$form->addError('Zvolená platební metoda není pro tuto dopravu k dispozici');
		}

		if (!$form->hasErrors()) {
			//$this->productService->saveProduct($data, $categories, $discounts, $this->id);

			// todo Vytvorit objednavku
			$this->newOrderFacade->processOrder();

			// todo IF paymentId == 1 (credit card), tak status bude stale paid=false a presmerujeme na stranku pro platbu
			// todo  teprve po OK obdrzeni platby nastavit paid=true, zobrazime   dekovaci stranku, posleme emaily...

			// todo ELSE tak paid=false (ci tak nejak) a redirect na              dekovaci stranku, posleme emaily...

		}
	}
}
