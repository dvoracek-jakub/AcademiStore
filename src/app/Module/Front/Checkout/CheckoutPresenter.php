<?php

declare(strict_types=1);

namespace App\Module\Front\Checkout;

use App\Model\Delivery\DeliveryService;
use App\Model\Order\NewOrderFacade;
use App\Module\Front\Accessory\Form\CheckoutFormFactory;

class CheckoutPresenter extends \App\Module\Front\BasePresenter
{

	/** @var CheckoutFormFactory */
	private CheckoutFormFactory $checkoutFormFactory;

	/** @var DeliveryService */
	private DeliveryService $deliveryService;

	/** @var NewOrderFacade */
	private NewOrderFacade $newOrder;

	public function injectCheckoutForm(CheckoutFormFactory $checkoutFormFactory)
	{
		$this->checkoutFormFactory = $checkoutFormFactory;
	}

	public function injectDeliveryService(DeliveryService $deliveryService)
	{
		$this->deliveryService = $deliveryService;
	}

	public function injectNewOrderFacade(NewOrderFacade $newOrderFacade)
	{
		$this->newOrder = $newOrderFacade;
	}

	public function startup()
	{
		parent::startup();
		$this->cartService->setCustomer($this->customer);
	}

	// Musi byt render* metoda, aby reflektovala zmeny z handle* signalu. Action* se totiz vola pred nim.
	public function renderOverview()
	{
		$cart = $this->cartService->getCurrentCart();

		if (!$cart) {
			$this->flashMessage('V košíku nejsou žádné položky');
			$this->redirect('Home:');
		}

		$totals = $this->deliveryService->recalculateTotals($this->cartService);

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
			$this->payload->isValidDeliveryCombination = $this->deliveryService->isValidDeliveryCombination($shippingId, $paymentId);
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

	public function actionPaymentGatewayCallback($id)
	{
		$this->newOrder->checkGatewayPaymentState($id);
	}

	public function actionCompleted(int $id, int $paid = 0)
	{
		// TODO check, jestli je jeho
		// TODO check, jestli je opravdu ve stavu NEW, jinak zobrazit jine hlasky (jakoze uz byla zpracovana, zrusena...)
	}

}