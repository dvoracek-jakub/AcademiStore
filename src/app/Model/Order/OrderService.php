<?php

namespace App\Model\Order;

use App\Model\Cart\CartService;
use App\Model\Delivery\Shipping\ShippingService;
use App\Model\Delivery\Payment\PaymentService;
use App\Model\Customer\Customer;
use App\Model\Order\OrderPayment;

class OrderService
{

	/** @var \App\Model\Order\OrderRepository|\Doctrine\ORM\EntityRepository */
	private $orderRepository;

	public function __construct(
		private \App\Model\EntityManagerDecorator $em,
		private CartService $cartService,
		private ShippingService $shippingService,
		private PaymentService $paymentService
	) {
		$this->orderRepository = $this->em->getRepository(\App\Model\Order\Order::class);
	}

	public function getOrder(int $id): ?Order
	{
		return $this->orderRepository->find($id);
	}

	/**
	 * Creates an order including all necessities
	 */
	public function createOrder(Customer $customer, int $shippingId, int $paymentId): Order
	{
		$shipping = $this->shippingService->getShipping($shippingId);
		$payment = $this->paymentService->getPayment($paymentId);
		$cart = $this->cartService->getCurrentCart();

		if (!$cart) {
			die("Košík je prázdný!"); // řešit lépěji
		}

		// Create order
		$order = new \App\Model\Order\Order();
		$order->setCustomer($customer);
		$order->setCart($cart);
		$order->setShippingType($shipping);
		$order->setPaymentType($payment);
		$order->setDeliveryAddress('domu');
		$this->em->persist($order);
		$this->em->flush();

		// Lock the cart
		$this->cartService->setStatus($cart, 'ORDERED');

		// Set payment status
		$paymentStatus = new \App\Model\Order\OrderPayment();
		$paymentStatus->setStatus('UNPAID');
		$paymentStatus->setOrder($order);
		$this->em->persist($paymentStatus);
		$this->em->flush();

		return $order;
	}

	public function updatePaymentStatus(?int $orderId, $paymentIdentifier, ?string $status = '', ?string $remoteState = '')
	{
		//bdump("orderid: $orderId | paymentIdentifier: $paymentIdentifier ", 'updatePaymentStatus');
		if ($orderId && $orderId > 0) {
			$orderPayment = $this->em->getRepository(OrderPayment::class)->findOneBy(['order' => $orderId]);
		} else {
			$orderPayment = $this->em->getRepository(OrderPayment::class)->findOneBy(['remoteIdentifier' => $paymentIdentifier]);
		}
		$orderPayment->setRemoteIdentifier($paymentIdentifier);
		if (!empty($status)) {
			$orderPayment->setStatus($status);
		}
		if (!empty($remoteState)) {
			$orderPayment->setRemoteState($remoteState);
		}
		$this->em->persist($orderPayment);
		$this->em->flush();
	}

	public function getOrderByRemoteIdentifier(string $paymentIdentifier)
	{
		$qb = $this->em->createQueryBuilder()
			->select('o', 'p')
			->from(\App\Model\Order\Order::class, 'o')
			->innerJoin('o.paymentStatus', 'p')
			->where('p.remoteIdentifier = :remoteIdentifier')
			->setParameter('remoteIdentifier', $paymentIdentifier);

		return $qb->getQuery()->getOneOrNullResult();
	}

}