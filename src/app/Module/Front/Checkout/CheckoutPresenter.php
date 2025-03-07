<?php

declare(strict_types=1);

namespace App\Module\Front\Checkout;

use App\Model\Delivery\DeliveryService;
use App\Model\Order\OrderService;
use App\Model\Order\NewOrderFacade;
use App\Module\Front\Accessory\Form\CheckoutFormFactory;

class CheckoutPresenter extends \App\Module\Front\BasePresenter
{

	/** @var CheckoutFormFactory */
	private CheckoutFormFactory $checkoutFormFactory;

	/** @var DeliveryService */
	private DeliveryService $deliveryService;

	/** @var OrderService */
	private OrderService $orderService;

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

	public function injectOrderService(OrderService $orderService)
	{
		$this->orderService = $orderService;
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
		$checkoutSession = $this->session->getSection('deliveryOptions');

		if (!$cart) {
			$this->flashMessage('V košíku nejsou žádné položky');
			$this->redirect('Home:');
		}

		$totals = $this->deliveryService->recalculateTotals($this->cartService);
		$this->template->cart = $cart;
		$this->template->totals = (object) $totals;
		$this->template->showAddressForm = ($checkoutSession->shippingId == 4 ? false : true);
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
		$customer = $this->customerService->getBy(['id' => $this->getUser()->getId()], true);
		$form = $this->checkoutFormFactory->createCheckoutForm($customer->getDataArray());
		return $form;
	}

	public function actionPaymentGatewayCallback($id)
	{
		try {
			$this->newOrder->checkGatewayPaymentState($id);
		} catch (\Exception $e) {
			$tpl = $this->getTemplate();
			$tpl->setFile(__DIR__ . '/payment-failed.latte');
			$tpl->state = $e->getMessage();
		}
	}

	public function actionCompleted(int $id, int $paid = 0)
	{
		if (!$this->getUser()->isLoggedIn() || (int) $id < 1) {
			$this->redirect('Home:');
		}
		$order = $this->orderService->getOrder($id);

		if ($order->getCustomer()->getId() !== $this->getUser()->getIdentity()->id) {
			$this->redirect('Home:');
		}

		$cart = $this->cartService->getCart($order->getCart()->getId());
		$this->template->order = $order;
		$this->template->totalPrice = $this->cartService->getCartTotals($cart)['withTax'];
	}

}