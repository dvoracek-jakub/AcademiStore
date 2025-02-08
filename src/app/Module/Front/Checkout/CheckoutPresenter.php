<?php

declare(strict_types=1);

namespace App\Module\Front\Checkout;

use App\Model\Delivery\DeliveryFacade;
use App\Module\Front\Accessory\Form\CheckoutFormFactory;

class CheckoutPresenter extends \App\Module\Front\BasePresenter
{

	/** @var CheckoutFormFactory */
	private CheckoutFormFactory $checkoutFormFactory;

	/** @var DeliveryFacade */
	private DeliveryFacade $deliveryFacade;

	public function injectCheckoutForm(CheckoutFormFactory $checkoutFormFactory)
	{
		$this->checkoutFormFactory = $checkoutFormFactory;
	}

	public function injectDeliveryFacade(DeliveryFacade $deliveryFacade)
	{
		$this->deliveryFacade = $deliveryFacade;
	}

	// Musi byt render* metoda, aby reflektovala zmeny z handle* signalu. Action* se totiz vola pred nim.
	public function renderOverview()
	{
		$cart = $this->cartFacade->getCurrentCart();

		if (!$cart) {
			$this->flashMessage('V košíku nejsou žádné položky');
			$this->redirect('Home:');
		}

		$totals = $this->deliveryFacade->recalculateTotals($this->cartFacade);

		$this->template->cart = $cart;
		$this->template->totals = (object) $totals;
	}

	public function handleDeliveryOptions()
	{
		$shippingId = isset($_POST['shippingId']) ? (int) $_POST['shippingId'] : 0;
		$paymentId = isset($_POST['paymentId']) ? (int) $_POST['paymentId'] : 0;
		$checkoutSession = $this->session->getSection('deliveryOptions');

		if ($shippingId > 0) {
			$checkoutSession->shippingId = $shippingId;
		}
		if ($paymentId > 0) {
			$checkoutSession->paymentId = $paymentId;
		}
		if ($this->isAjax()) {
			$this->redrawControl('deliveryOptions');
			$this->payload->isValidDeliveryCombination = $this->deliveryFacade->isValidDeliveryCombination($shippingId, $paymentId);
		}
	}

	public function createComponentCheckoutForm()
	{
		$form = $this->checkoutFormFactory->createCheckoutForm();
		$form->onSuccess[] = function(): void {
			// Tady asi nic, různé redirecty na platební brány apod se budou dít v checkoutformfactory
		};
		return $form;
	}

}