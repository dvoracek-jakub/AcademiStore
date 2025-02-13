<?php

declare(strict_types=1);

namespace App\Module\Front\Test;

use App\Model\Order\OrderService;
use App\Module\Front\Accessory\FormFactory;
use App\UI\Admin\Test\Nette;
use Nette\Application\UI\Form;
use App\Core\Authenticator\CustomerAuthenticator;
use Nette\Application\LinkGenerator;
use Nette\Application\Responses\RedirectResponse;

final class TestPresenter extends \Nette\Application\UI\Presenter
{
	/** @var OrderService */
	private $orderService;

	public function __construct(
		private LinkGenerator $linkGenerator,
		private \App\Model\EntityManagerDecorator $em,
	) {}

	public function injectCategoryService(OrderService $orderService)
	{
		$this->orderService = $orderService;
	}

	public function actionAlfa()
	{
		$orderPayment = $this->em->getRepository(\App\Model\Order\OrderPayment::class)->findOneBy(['order' => 33]);
		echo $orderPayment->getStatus();
		bdump($orderPayment);
		die("<br>\n".time().' ['.__LINE__."] ");
	}

	public function actionBravo()
	{
	    $order = $this->orderService->getOrderByRemoteIdentifier('3274729263');
		bdump($order, 'ORDER actionBravo');
		die("<br>\n".time().' ['.__LINE__."] ");
	}
}