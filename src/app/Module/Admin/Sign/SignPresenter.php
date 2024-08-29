<?php

declare(strict_types=1);

namespace App\Module\Admin\Sign;

use App\Module\Admin\Accessory\FormFactory;
use App\UI\Admin\Sign\Nette;
use Nette\Application\UI\Form;

final class SignPresenter extends \Nette\Application\UI\Presenter
{

	public function __construct(private FormFactory $formFactory) {}

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
				$this->getUser()->login($data->username, $data->password);
				$this->redirect('Dashboard:');
			} catch (Nette\Security\AuthenticationException) {
				$form->addError('The username or password you entered is incorrect.');
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
		$this->redirect('sign:in');
	}

}