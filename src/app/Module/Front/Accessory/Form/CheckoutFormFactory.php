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

	public function createCheckoutForm(?array $defaults = []): Form
	{
		$form = new Form();
		$shippings = $this->shippingService->getShippingsArray(true);
		$payments = $this->paymentService->getPaymentsArray(true);
		$form->addRadioList('shippingId', 'Doprava', $shippings)
			->setRequired('Vyberte dopravu');
		$form->addRadioList('paymentId', 'Platba', $payments)
			->setRequired('Vyberte platbu');

		$form->addText('firstname', 'Jméno')
			->setRequired('Vyplňte vaše jméno');
		$form->addText('lastname', 'Příjmení')
			->setRequired('Vyplňte vaše příjmení');
		$form->addText('phone', 'Telefon')
			->setRequired('Vyplňte váš telefon');
		$form->addText('street', 'Ulice a čp.')
			->setRequired('Vyplňte ulici');
		$form->addText('city', 'Město')
			->setRequired('Vyplňte město');
		$form->addText('zip', 'PSČ')
			->setRequired('Vyplňte PSČ');
		$form->addSubmit('submit', 'Odeslat objednávku');

		$checkout = $this->session->getSection('deliveryOptions');
		if ($checkout->shippingId > 0) {
			$form['shippingId']->setDefaultValue($checkout->shippingId);
		}
		if ($checkout->paymentId > 0) {
			$form['paymentId']->setDefaultValue($checkout->paymentId);
		}

		$form->setDefaults($defaults);
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
