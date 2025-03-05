<?php

namespace App\Module\Front\Accessory\Form;

use App\Model\Customer\CustomerService;
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
		private \Nette\Http\Session $session,
		private \Nette\Security\User $user,
		private CustomerService $customerService
	) {}

	public function createCheckoutForm(): Form
	{
		$form = new Form();
		$shippings = $this->shippingService->getShippingsArray(true);
		$payments = $this->paymentService->getPaymentsArray(true);
		$form->addRadioList('shippingId', 'Doprava', $shippings);
		$form->addRadioList('paymentId', 'Platba', $payments);

		$form->addText('firstname', 'Jméno');
		$form->addText('lastname', 'Příjmení');
		$form->addText('phone', 'Telefon');
		$form->addText('street', 'Ulice a čp.');
		$form->addText('city', 'Město');
		$form->addText('zip', 'PSČ');

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
		if (!$this->deliveryService->isValidDeliveryCombination($data->shippingId, $data->paymentId)) {
			$form->addError('Zvolená platební metoda není pro tuto dopravu k dispozici.');
		}

		$customerId = (int) $this->user->getIdentity()->id;
		if ($customerId < 1) {
			$form->addError('Nepřihlášený uživatel nemůže vytvářet objednávky.');
		}

		if (!$form->hasErrors()) {
			$customer = $this->customerService->getCustomer($customerId);
			$this->newOrderFacade->processOrder($customer, $data);
		}
	}

}
