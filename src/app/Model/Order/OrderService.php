<?php

namespace App\Model\Order;

use App\Model\Delivery\Shipping\ShippingService;
use App\Model\Delivery\Payment\PaymentService;
use App\Model\Customer\Customer;

class OrderService
{

	/** @var \App\Model\Order\OrderRepository|\Doctrine\ORM\EntityRepository */
	private $orderRepository;

	public function __construct(
		private \App\Model\EntityManagerDecorator $em,
		private ShippingService $shippingService,
		private PaymentService $paymentService
	) {
		$this->orderRepository = $this->em->getRepository(\App\Model\Order\Order::class);
	}

	public function getOrder(int $id): ?Order
	{
		return $this->orderRepository->find($id);
	}

	/*
 *  Vytvoří objednávku
 *
 */
	public function createOrder(Customer $customer): Order
	{
		$shippingId = 2;  //todo predavat
		$paymentId = 2;  //todo predavat

		$shipping = $this->shippingService->getShipping($shippingId);
		$payment = $this->paymentService->getPayment($paymentId);

		$order = new \App\Model\Order\Order();
		$order->setCustomer($customer);
		$order->setShipping($shipping);
		$order->setPayment($payment);
		$order->setDeliveryAddress('domu');

		// todo predavat car


		$this->em->persist($order);
		$this->em->flush();

		die("<br>\n".time().' ['.__LINE__."] ORDER CREATED ? ");

		// NASTAVIT cart.status jako ORDERED

		// TODO krome samotne order vytvori i zaznam v order_payment. Status zde vzdy = UNPAID

		return $order;
	}

}