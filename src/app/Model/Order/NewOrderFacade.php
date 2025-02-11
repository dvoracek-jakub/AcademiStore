<?php

declare(strict_types=1);

namespace App\Model\Order;

use App\Core\Payment\GoPayDecorator;
use App\Model\Customer\Customer;
use App\Model\Order\Order;

class NewOrderFacade
{

	public function __construct(private GoPayDecorator $goPay) {}

	/**
	 * @TODO udělej to tak, aby nová objednávka šla vytvořit i třeba z admina
	 *       Tedy ať všechny potřebné parametry přebírá a je mu jedno, kdo mu je zaslal
	 *        - jestli to byl formulář na frontendu, nebo třeba API
	 */

	public function processOrder(Customer $customer, int $shippingId, int $paymentId)
	{

		bdump($customer);
		die("<br>\n".time().' ['.__LINE__."] ");

		// TODO Vzdy jako prvni vytvorime objednavku
		$order = $this->createOrder();

		// Dale viz dokumentace

		die("<br>\n" . time() . ' [' . __LINE__ . "] processOrder() here");
	}

	/**
	 * zkontroluje, jestli je objednavka v naprostem poradku
	 */
	public function checkGatewayPaymentState()
	{

	}

	/*
	 *  Vytvoří objednávku
	 *
	 */
	public function createOrder(): Order
	{
		$order;
		/*
		 * TODO Vytvořit objednávku,
		 */


		// TODO krome samotne order vytvori i zaznam v order_payment. Status zde vzdy = UNPAID

		return $order;
	}

	public function success()
	{

	}

}