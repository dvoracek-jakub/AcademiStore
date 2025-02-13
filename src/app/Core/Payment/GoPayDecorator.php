<?php

declare(strict_types=1);

namespace App\Core\Payment;


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
		$this->returnUrl = $options->returnUrl;
		$this->notificationUrl = $options->notificationUrl;
	}

	public function createPayment(int $orderNumber, float|int $price, string $customerEmail, $afterPaymentCreated)
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

		$response = $this->gw->createPayment($payment);
		$data = (object) $response->json;

		if ($data->state == 'CREATED') {

			// Update order_payment status
			$afterPaymentCreated($data->id);

			Header("Location: $data->gw_url");
			exit;
		} else {
			die('err: ads41f9'); // řešit lépěji
		}
	}


	public function getStatus($paymentIdentifier)
	{
		$response = $this->gw->getStatus($paymentIdentifier);
		return (object) $response->json;
	}

}