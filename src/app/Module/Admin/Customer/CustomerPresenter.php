<?php

declare(strict_types=1);

namespace App\Module\Admin\Customer;

use App\Model\Customer\CustomerService;

class CustomerPresenter extends \App\Module\Admin\BasePresenter
{

	/** @var \App\Model\Customer\CustomerService */
	private $customerService;

	/** @var \App\Model\Customer\CustomerRepository */
	private $customerRepository;

	public function injectCustomerService(CustomerService $customerService)
	{
		$this->customerService = $customerService;
		$this->customerRepository = $this->em->getRepository(\App\Model\Customer\Customer::class);
	}

	public function renderList()
	{
		$customers = $this->customerService->getBy();
		$this->template->customers = $customers;
	}

	public function createComponentCustomerList($name)
	{
		$grid = new \Contributte\Datagrid\Datagrid($this, $name);
		$grid->setDataSource($this->customerRepository->createQueryBuilder('c'));
		$grid->addColumnLink('firstname', 'Name')->setRenderer(function($item) {
			return '<a href="' . $this->link('Customer:detail', $item->getId()) . '">' .
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
		$this->template->customer = $this->customerService->getBy(['id' => $id], true);
		$cartRepository = $this->em->getRepository(\App\Model\Cart\Cart::class);
		$this->template->currentCart =  $cartRepository->getCart(null, $id, 'NEW');
		bdump($this->template->currentCart);
	}

}