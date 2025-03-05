<?php

declare(strict_types=1);

namespace App\Module\Front\Accessory\Form;

use App\Model\Customer\CustomerService;
use Nette\Application\UI\Form;

class CustomerDetailFormFactory
{

	public function __construct(public CustomerService $customerService, private \Nette\Security\User $user) {}

	public function create(?array $defaults): Form
	{
		$form = new Form();
		$form->addText('firstname', 'Jméno');
		$form->addText('lastname', 'Příjmení');
		$form->addText('phone', 'Telefon');

		$form->addPassword('password_new', 'Nové heslo');
		$form->addPassword('password_new_check', 'Potvrzení nového hesla')
			->addConditionOn($form['password_new'], $form::MinLength, 1)
			->setRequired('Při změně hesla je nutné jej potvrdit');

		$form->addPassword('password_old', 'Staré heslo')
			->addConditionOn($form['password_new'], $form::MinLength, 1)
			->setRequired('Pro změnu hesla je nutné zadat dosavadní heslo');

		$form->addText('street', 'Ulice');
		$form->addText('city', 'Město');
		$form->addText('zip', 'PSČ');

		$form->setDefaults($defaults);

		$form->addSubmit('send', 'Odeslat');
		$form->onSuccess[] = [$this, 'formSucceeded'];
		return $form;
	}

	public function formSucceeded(Form $form, $data)
	{
		$customerId = $this->user->getIdentity()->getId();
		if (!empty($data->password_new)) {
			$customer = $this->customerService->getBy(['id' => $customerId], true);
			if (!password_verify($data->password_old, $customer->getPassword())) {
				$form->addError('Bylo zadáno špatné současné heslo');
			}

			if ($data->password_new !== $data->password_new_check) {
				$form->addError('"Nové heslo" a "Potvrzení nového hesla" neobsahují stejné heslo');
			}
		}

		if (!$form->hasErrors()) {
			$data['password'] = $data->password_new;
			$this->customerService->saveCustomer($data, $customerId);
			$this->customerService->saveAddress($data, $customerId);
		}
	}


}