<?php

declare(strict_types=1);

namespace App\Module\Admin\Order;

use App\Model\Order\OrderService;
use App\Model\Cart\CartService;
use App\Model\Product\ProductPrice;

class OrderPresenter extends \App\Module\Admin\BasePresenter
{

	/** @var \App\Model\Order\OrderService */
	private $orderService;

	/** @var \App\Model\Order\OrderRepository */
	private $orderRepository;

	/** @var \App\Model\Cart\CartService */
	private $cartService;

	/** @var \App\Model\Product\ProductPrice */
	private $productPrice;

	public function injectOrderService(OrderService $orderService)
	{
		$this->orderService = $orderService;
		$this->orderRepository = $this->em->getRepository(\App\Model\Order\Order::class);
	}

	public function injectCartService(CartService $cartService)
	{
		$this->cartService = $cartService;
	}

	public function injectProductPrice(ProductPrice $productPrice)
	{
		$this->productPrice = $productPrice;
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

		$grid->addColumnLink('id', 'Number');
		
		$grid->addColumnText('created_at', 'Created')->setRenderer(function($item) {
			return '<a href="' . $this->link('Order:detail', $item->getId()) . '">' .
				$item->getCreatedAt()->format('Y-m-d H:i') . '</a>';
		})->setTemplateEscaping(false);

		$grid->addColumnText('customer_id', 'sd')->setRenderer(function($item) {
			return '<a href="' . $this->link('Customer:detail', $item->getCustomer()->getId()) . '">' .
				$item->getCustomer()->getLastname() . ' ' . $item->getCustomer()->getFirstname() . '</a>';
		})->setTemplateEscaping(false);

		$grid->addColumnText('totals', 'Total price')->setRenderer(function($item) {
			return $this->productPrice->format($this->cartService->getCartTotals($item->getCart())['withTax']);
		});

		$grid->addColumnText('payment_status', 'Payment status')->setRenderer(function($item) {
			$orderPayment = $item->getPaymentStatus();
			if ($orderPayment) {
				return $item->getPaymentStatus()->getStatus();
			}
			return '-';
		});

		$grid->addColumnStatus('status', 'Status')
			->addOption('NEW', 'NEW')
			->setClass('btn-primary')
			->endOption()
			->addOption('IN_PROCESS', 'IN_PROCESS')
			->setClass('btn-warning')
			->endOption()
			->addOption('CANCELLED', 'CANCELLED')
			->setClass('btn-danger')
			->endOption()
			->addOption('RETURNED', 'RETURNED')
			->setClass('btn-danger')
			->endOption()
			->addOption('COMPLETED', 'COMPLETED')
			->setClass('btn-success')
			->endOption()
			->onChange[]
			= [$this, 'changeStatus'];
		$grid->setDefaultPerPage(20);
	}

	public function changeStatus($id, $newStatus)
	{
		$this->orderService->updateStatus((int) $id, $newStatus);
		if ($this->isAjax()) {
			$this['orderList']->redrawItem($id);
		}
	}

	public function actionDetail(int $id)
	{
		$this->template->order = $this->orderService->getBy(['id' => $id], true);
	}

}