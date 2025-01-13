<?php

declare(strict_types=1);

namespace App\Module\Front\Home;

use App\DB\Entity\User as UserEntity;
use App\DB\EntityManagerDecorator;
use App\DB\Entity\User;
use Nette;

final class HomePresenter extends Nette\Application\UI\Presenter
{

	//#[Inject]
	//public EntityManagerDecorator $em;

	public function __construct(public EntityManagerDecorator $em) {}

	public function actionDefault()
	{

	}

	/**
	 * Doctrine pokusy
	 *
	 * @return void
	 */
	public function actionDoctrine()
	{

		// Řádek s konkrétním id (primary key)
		$user = $this->em->getRepository(UserEntity::class)->find(1);
		echo $user->getLastname();

		// Vrátí jeden řádek podle kritérií. Možno orderBy.
		$user = $this->em->getRepository(UserEntity::class)->findOneBy(['username' => 'admin']);
		echo $user->getId();

		// Vrátí všechny řádky podle kritérií. Možno orderBy.
		$users = $this->em->getRepository(UserEntity::class)->findBy(['id' => 1, 'username' => 'admin'], ['username' => 'DESC']);
		foreach ($users as $user) {
			echo $user->getFirstname();
		}

		// Vrátí všechny řádky. Řazení zadat nejde - alias pro findBy([])
		$users = $this->em->getRepository(UserEntity::class)->findAll();
		foreach ($users as $user) {
			echo $user->getFirstname();
		}

		// Update lastname
		$user = $this->em->getRepository(UserEntity::class)->find(1);
		$user->setLastname('Vyndal');
		$this->em->flush();

		// Insert new
		/*$newUser = new UserEntity();
		$newUser->setUsername('noem');
		$newUser->setPassword('simulacra');
		$this->em->persist($newUser);
		$this->em->flush();*/

		// Delete
		$user = $this->em->getRepository(UserEntity::class)->find(20);
		isset($user) && $this->em->remove($user) | $this->em->flush();


		die("<br>\n".time().' ['.__LINE__."] ");
	}

}
