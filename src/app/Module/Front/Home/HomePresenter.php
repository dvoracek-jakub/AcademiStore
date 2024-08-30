<?php

declare(strict_types=1);

namespace App\Module\Front\Home;

use App\DB\EntityManagerDecorator;
use Nette;


final class HomePresenter extends Nette\Application\UI\Presenter
{

	//#[Inject]
	//public EntityManagerDecorator $em;

	public function __construct(public EntityManagerDecorator $em) {}

	public function actionDefault()
	{
		$this->template->users = $this->em->getRepository(\App\DB\Entity\User::class)->findAll();
		foreach ($this->template->users as $user) {
			echo $user->getUsername();
		}
		die("<br>\n".time().' ['.__LINE__."] ");
	}

}
