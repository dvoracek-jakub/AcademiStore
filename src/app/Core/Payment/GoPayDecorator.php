<?php

declare(strict_types=1);

namespace App\Core\Payment;


use JetBrains\PhpStorm\NoReturn;

class GoPayDecorator
{
	private $gw;

	private string $returnUrl;

	private string $notificationUrl;

	public function __construct(array $options)
	{
		$options = (object) $options;
		$this->gw = \GoPay\Api::payments([
			'goid'         => $options->goId,
			'clientId'     => $options->clientId,
			'clientSecret' => $options->clientSecret,
			'gatewayUrl'   => $options->gatewayUrl,
		]);
	}


	#[NoReturn] public function createPayment(int $orderNumber, float $price, string $customerEmail)
	{
		$price = $price * 100;
		$payment = [
			'amount'       => $price,
			'currency'     => \GoPay\Definition\Payment\Currency::CZECH_CROWNS,
			'order_number' => $orderNumber,
			'payer'        => [
				'contact' => [
					'email' => $customerEmail,
				],
			],
			'callback'     => [
				'return_url'       => $this->returnUrl,
				'notification_url' => $this->notificationUrl,
			],
		];

		$response = $gopay->createPayment($payment);

		bdump($response);
		print_r($response);
		die(" |Line: " . __LINE__);

	}

}