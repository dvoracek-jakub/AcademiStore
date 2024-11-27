<?php

declare(strict_types=1);
namespace App\Module\Admin;

use Nette;

class BasePresenter extends Nette\Application\UI\Presenter
{

	public function __construct(public \App\Model\EntityManagerDecorator $em)
	{
	    
	}

	public function startup()
	{
		parent::startup();
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Sign:in');
		}
	}

}