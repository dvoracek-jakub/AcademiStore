<?php

namespace App\Module\Front\Accessory\Form;

use App\Model\Customer\CustomerService;
use Nette\Application\UI\Form;

class SignUpFormFactory
{

	public function __construct(
		private \Nette\Http\Session $session,
		private \Nette\Security\User $user,
		private CustomerService $customerService
	) {}

	public function createSignUpForm(): Form
	{
		$form = new Form();
		$form->addText('email', 'E-mail:')
			->setRequired('Zadejte,vaši e-mailovou adresu')
			->addRule($form::EMAIL, 'Zadejte platnou e-mailovou adresu');

		$form->addPassword('password', 'Heslo:')
			->setRequired('Je nutné zadat heslo');

		$form->addPassword('password2', 'Heslo pro kontrolu:')
			->setRequired('Je nutné zadat heslo pro kontrolu');

		$form->addSubmit('submit', 'Registrovat');
		$form->onSuccess[] = [$this, 'formSucceeded'];
		return $form;

	}

	public function formSucceeded(Form $form, $data)
	{
		bdump($data, 'Customer Registration Form sent');
		if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
			$form->addError('Neplatná e-mailová adresa');
		}

		if ($data->password != $data->password2) {
			$form->addError('Hesla se neshodují');
		}

		$existing = $this->customerService->getBy(['email' => $data->email]);
		if ($existing) {
			$form->addError('Uživatelský účet se zadaným emailem již existuje');
		}

		if (!$form->hasErrors()) {
			$this->customerService->saveCustomer($data);
		}
	}

}
