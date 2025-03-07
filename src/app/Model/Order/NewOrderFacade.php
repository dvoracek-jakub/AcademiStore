<?php

declare(strict_types=1);

namespace App\Model\Order;

use App\Model\Order\Order;
use App\Model\Cart\CartService;
use App\Model\Order\OrderService;
use App\Model\Customer\Customer;
use Nette\Application\LinkGenerator;
use Nette\Application\Responses\RedirectResponse;
use App\Core\Payment\GoPayDecorator;

class NewOrderFacade
{

	public function __construct(
		private GoPayDecorator $goPay,
		private OrderService $orderService,
		private CartService $cartService,
		private LinkGenerator $linkGenerator
	) {}

	public function processOrder(Customer $customer, $data)
	{
		$cart = $this->cartService->getCurrentCart();
		$totalPrice = $this->cartService->getCartTotals($cart);

		// Create new order
		// @var \App\Model\Order\Order
		$order = $this->orderService->createOrder($customer, $data);
		bdump("Crated order: " . $order->getId(), 'New Order Created');

		// Save delivery data
		$this->orderService->saveDeliveryData($order, $customer, $data);

		// Is card payment
		if ($data->paymentId === 1) {
			$orderId = $order->getId();
			$afterPaymentCreated = function($paymentIdentifier) use ($orderId) {
				$this->orderService->updatePaymentStatus($orderId, $paymentIdentifier, 'CREATED');
			};
			$this->goPay->createPayment($order->getId(), $totalPrice['withTax'], $customer->getEmail(), $afterPaymentCreated);
			die('STATREP/X8V1W');
		} else {
			$this->success($order);
		}

	}

	/**
	 * Checks if the order payment is in perfect order
	 */
	public function checkGatewayPaymentState($paymentIdentifier)
	{
		$status = $this->goPay->getStatus($paymentIdentifier);
		if ($status->state == 'PAID') {
			$this->orderService->updatePaymentStatus(null, $status->id, 'PAID', $status->state);

			$order = $this->orderService->getOrderByRemoteIdentifier($paymentIdentifier);
			$this->success($order, true);
		} else {
			$this->orderService->updatePaymentStatus(null, $status->id, null, $status->state);
			throw new \Exception($status->state);
		}
	}

	/**
	 * Let's celebrate
	 */
	public function success(Order $order, bool $justPaid = false)
	{
		// TODO CALL @injected $emailer->confirmationEmail($order)

		$filta = ['id' => $order->getId()];
		if ($justPaid) {
			$filta['paid'] = 1;
		}
		$url = $this->linkGenerator->link('Front:Checkout:completed', $filta);
		Header("Location: $url");
		exit;
		// Nefunguje:
		return new RedirectResponse($url);
		exit;
	}

}