<?php

declare(strict_types=1);

namespace App\Module\Front\Sign;

use App\Module\Front\Accessory\FormFactory;
use App\Module\Front\Accessory\Form\SignUpFormFactory;
use App\UI\Admin\Sign\Nette;
use Nette\Application\UI\Form;
use App\Core\Authenticator\CustomerAuthenticator;

final class SignPresenter extends \Nette\Application\UI\Presenter
{

	public function __construct(
		private FormFactory $formFactory,
		private CustomerAuthenticator $authenticator,
		private SignUpFormFactory $signUpFormFactory
	) {}

	/**
	 * Create a sign-in form with fields for username and password.
	 * On successful submission, the user is redirected to the dashboard or back to the previous page.
	 */
	protected function createComponentSignInForm(): Form
	{
		$form = $this->formFactory->create();
		$form->addText('email', 'E-mail:')
			->setRequired('Zadejte vaši e-mailovou adresu')
			->addRule($form::EMAIL, 'Zadejte platnou e-mailovou adresu');

		$form->addPassword('password', 'Password:')
			->setRequired('Je nutné zadat heslo');

		$form->addSubmit('submit', 'Sign in');

		// Handle form submission
		$form->onSuccess[] = function(Form $form, \stdClass $data): void {
			try {
				$identity = $this->authenticator->authenticate($data->email, $data->password);
				$this->getUser()->login($identity);
				$this->redirect('Home:');
			} catch (\Nette\Security\AuthenticationException $e) {
				$form->addError($e->getMessage());
			}
		};
		return $form;
	}

	protected function createComponentSignUpForm(): Form
	{
		$form = $this->signUpFormFactory->createSignUpForm();
		return $form;
	}

	/**
	 * Logs out the currently authenticated user.
	 */
	public function actionOut(): void
	{
		$this->flashMessage('Successfully Logged out.');
		$this->getUser()->logout();
		$this->redirect('Home:');
	}

}