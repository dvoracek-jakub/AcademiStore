<?php

declare(strict_types=1);
namespace App\Module\Admin;

use Nette;

class BasePresenter extends Nette\Application\UI\Presenter
{

	public function __construct(protected \App\Model\EntityManagerDecorator $em, protected \App\Core\Settings $settings) {}

	public function startup()
	{
		parent::startup();
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Sign:in');
		}
	}

	public function beforeRender()
	{
		$this->template->product_image_path = $this->settings->store->product_image_path;
	}

}