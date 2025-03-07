<?php

declare(strict_types=1);

namespace App\Module\Front\Customer;

use \App\Module\Front\Accessory\Form\CustomerDetailFormFactory;

final class CustomerPresenter extends \App\Module\Front\BasePresenter
{

	/** @var CustomerDetailFormFactory */
	private $customerDetailFormFactory;

	public function injectCustomerDetailForm(CustomerDetailFormFactory $customerDetailFormFactory)
	{
		$this->customerDetailFormFactory = $customerDetailFormFactory;
	}

	public function renderDetail() {}

	public function createComponentCustomerDetailFrom()
	{

		$customer = $this->customerService->getBy(['id' => $this->getUser()->getId()])[0];
		$defaults['firstname'] = $customer->getFirstname();
		$defaults['lastname'] = $customer->getLastname();
		$defaults['phone'] = $customer->getPhone();

		if ($customer->getAddress()) {
			$defaults['street'] = $customer->getAddress()->getStreet();
			$defaults['city'] = $customer->getAddress()->getCity();
			$defaults['zip'] = $customer->getAddress()->getZip();
		}

		$form = $this->customerDetailFormFactory->create($defaults);
		$form->onSuccess[] = function() {
			$this->flashMessage('Změny byly uloženy.');
		};
		return $form;
	}

}