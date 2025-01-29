<?php declare(strict_types=1);

namespace App\Model\Cart;

use Doctrine\DBAL\Connection;
use Nettrine\Extra\Repository\AbstractRepository;

/**
 * @extends AbstractRepository<\App\Model\Cart\Cart>
 * @method Cart|null find($id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method Cart|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cart|null findOneById(int $id)
 * @method Cart[] findAll()
 * @method Cart[] findBy(array $criteria, array $orderBy = null, ?int $limit = null, ?int $offset = null)
 */
final class CartRepository extends AbstractRepository
{

	private Connection $connection;

	public function __construct(
		private \Doctrine\ORM\EntityManager $em,
		private \Doctrine\ORM\Mapping\ClassMetadata $class
	) {
		parent::__construct($em, $class);
		$this->connection = $em->getConnection();
	}

}
