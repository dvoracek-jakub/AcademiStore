<?php

namespace App\Model\Order;

use App\Model\Cart\CartService;
use App\Model\Delivery\Shipping\ShippingService;
use App\Model\Delivery\Payment\PaymentService;
use App\Model\Order\Order;
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
		$this->orderRepository = $this->em->getRepository(Order::class);
	}

	public function getOrder(int $id): ?Order
	{
		return $this->orderRepository->find($id);
	}

	public function getBy(array $criteria = [], bool $single = false)
	{
		if ($single) {
			return $this->orderRepository->findOneBy($criteria);
		} else {
			return $this->orderRepository->findBy($criteria);
		}
	}

	/**
	 * Creates an order including all necessities
	 */
	public function createOrder(Customer $customer, $data): Order
	{
		$shipping = $this->shippingService->getShipping($data->shippingId);
		$payment = $this->paymentService->getPayment($data->paymentId);
		$cart = $this->cartService->getCurrentCart();

		if (!$cart) {
			die("Košík je prázdný!"); // řešit lépěji
		}

		// Create order
		$order = new Order();
		$order->setCustomer($customer);
		$order->setCart($cart);
		$order->setShippingType($shipping);
		$order->setPaymentType($payment);
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

	public function saveOrderDeliveryData(int $orderId, $data)
	{
		$delivery = new \App\Model\Order\OrderDeliveryData();
	}

	public function updateStatus(int $orderId, string $status)
	{
		$order = $this->em->getRepository(Order::class)->findOneBy(['id' => $orderId]);
		$order->setStatus($status);
		$this->em->persist($order);
		$this->em->flush();
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
			->from(Order::class, 'o')
			->innerJoin('o.paymentStatus', 'p')
			->where('p.remoteIdentifier = :remoteIdentifier')
			->setParameter('remoteIdentifier', $paymentIdentifier);

		return $qb->getQuery()->getOneOrNullResult();
	}

}