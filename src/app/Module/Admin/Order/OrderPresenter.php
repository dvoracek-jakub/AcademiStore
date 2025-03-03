<?php

declare(strict_types=1);

namespace App\Module\Admin\Order;

use App\Model\Order\OrderService;

class OrderPresenter extends \App\Module\Admin\BasePresenter
{

	/** @var \App\Model\Order\OrderService */
	private $orderService;

	/** @var \App\Model\Order\OrderRepository */
	private $orderRepository;

	public function injectOrderService(OrderService $orderService)
	{
		$this->orderService = $orderService;
		$this->orderRepository = $this->em->getRepository(\App\Model\Order\Order::class);
	}

	public function renderList()
	{
		$orders = $this->orderService->getBy();
		$this->template->orders = $orders;
	}

	public function createComponentOrderList($name)
	{
		$grid = new \Contributte\Datagrid\Datagrid($this, $name);
		$grid->setDataSource($this->orderRepository->createQueryBuilder('c'));
		$grid->addColumnLink('firstname', 'Name')->setRenderer(function($item) {
			return '<a href="' . $this->link('Order:detail', $item->getId()) . '">' .
				$item->getLastname() . ' ' . $item->getFirstname() . '</a>';
		})->setTemplateEscaping(false);
		$grid->addColumnText('email', 'E-mail')->setRenderer(function($item) {
			return '<a href="mailto:' . $item->getEmail() . '">' . $item->getEmail() . '</a>';
		})->setTemplateEscaping(false);

		$grid->addColumnText('created_at', 'Created')->setRenderer(function($item) {
			return $item->getCreatedAt()->format('Y-m-d');
		});
		$grid->setDefaultPerPage(20);
	}

	public function actionDetail(int $id)
	{
		$this->template->order = $this->orderService->getBy(['id' => $id], true);
	}

}