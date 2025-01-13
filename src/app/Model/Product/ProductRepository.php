<?php declare(strict_types = 1);

namespace App\Model\Product;

use Doctrine\DBAL\Connection;
use Nettrine\Extra\Repository\AbstractRepository;

/**
 * @extends AbstractRepository<\App\Model\Product\Product>
 * @method User|null find($id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User|null findOneById(int $id)
 * @method User[] findAll()
 * @method User[] findBy(array $criteria, array $orderBy = null, ?int $limit = null, ?int $offset = null)
 */
final class ProductRepository extends AbstractRepository
{

	private Connection $connection;

	public function __construct(private \Doctrine\ORM\EntityManager $em, private \Doctrine\ORM\Mapping\ClassMetadata $class)
	{
		parent::__construct($em, $class);
		$this->connection = $em->getConnection();
	}

	public function getProducts()
	{
		$sql = "SELECT * FROM product";
		$stmt = $this->connection->prepare($sql);
		return $stmt->executeQuery()->fetchAllAssociative();   // fetchAssociative() ...

	}

}
