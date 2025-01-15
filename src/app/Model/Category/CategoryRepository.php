<?php declare(strict_types=1);

namespace App\Model\Category;

use Doctrine\DBAL\Connection;
use Nettrine\Extra\Repository\AbstractRepository;

/**
 * @extends AbstractRepository<\App\Model\Category\Category>
 * @method Category|null find($id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category|null findOneById(int $id)
 * @method Category[] findAll()
 * @method Category[] findBy(array $criteria, array $orderBy = null, ?int $limit = null, ?int $offset = null)
 */
final class CategoryRepository extends AbstractRepository
{

	private Connection $connection;

	public function __construct(private \Doctrine\ORM\EntityManager $em, private \Doctrine\ORM\Mapping\ClassMetadata $class)
	{
		parent::__construct($em, $class);
		$this->connection = $em->getConnection();
	}

	public function getCategories()
	{
		$sql = "SELECT * FROM category";
		$stmt = $this->connection->prepare($sql);
		return $stmt->executeQuery()->fetchAllAssociative();   // fetchAssociative() ...

	}

}
