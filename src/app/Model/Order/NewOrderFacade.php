<?php

declare(strict_types=1);

namespace App\Model\Order;

use App\Core\Payment\GoPayDecorator;
use App\Model\Order\OrderService;
use App\Model\Customer\Customer;
use App\Model\Order\Order;

class NewOrderFacade
{

	public function __construct(private GoPayDecorator $goPay,
		private OrderService $orderService,
	) {}

	/**
	 * @TODO udělej to tak, aby nová objednávka šla vytvořit i třeba z admina
	 *       Tedy ať všechny potřebné parametry přebírá a je mu jedno, kdo mu je zaslal
	 *        - jestli to byl formulář na frontendu, nebo třeba API
	 */

	public function processOrder(Customer $customer, int $shippingId, int $paymentId)
	{

		bdump($customer);

		// TODO Vzdy jako prvni vytvorime objednavku
		$order = $this->orderService->createOrder($customer);

		// Dale viz dokumentace

		die("<br>\n" . time() . ' [' . __LINE__ . "] processOrder() here");
	}

	/**
	 * zkontroluje, jestli je objednavka v naprostem poradku
	 */
	public function checkGatewayPaymentState() {}



	public function success() {}

}