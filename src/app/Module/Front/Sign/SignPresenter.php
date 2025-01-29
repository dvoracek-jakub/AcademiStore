<?php

declare(strict_types=1);

namespace App\Module\Front\Sign;

use App\Module\Front\Accessory\FormFactory;
use App\UI\Admin\Sign\Nette;
use Nette\Application\UI\Form;
use App\Core\Authenticator\CustomerAuthenticator;

final class SignPresenter extends \Nette\Application\UI\Presenter
{

	public function __construct(
		private FormFactory $formFactory,
		private CustomerAuthenticator $authenticator
	) {}

	/**
	 * Create a sign-in form with fields for username and password.
	 * On successful submission, the user is redirected to the dashboard or back to the previous page.
	 */
	protected function createComponentSignInForm(): Form
	{
		$form = $this->formFactory->create();
		$form->addText('username', 'Username:')
			->setRequired('Please enter your username.');

		$form->addPassword('password', 'Password:')
			->setRequired('Please enter your password.');

		$form->addSubmit('send', 'Sign in');

		// Handle form submission
		$form->onSuccess[] = function(Form $form, \stdClass $data): void {
			try {
				$identity = $this->authenticator->authenticate($data->username, $data->password);
				$this->getUser()->login($identity);
				$this->redirect('Home:');
			} catch (\Nette\Security\AuthenticationException $e) {
				$form->addError($e->getMessage());
			}
		};

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