<?php declare(strict_types=1);

namespace App\Model\Customer;

use Doctrine\DBAL\Connection;
use Nettrine\Extra\Repository\AbstractRepository;

/**
 * @extends AbstractRepository<\App\Model\Customer\Customer>
 * @method Customer|null find($id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer|null findOneById(int $id)
 * @method Customer[] findAll()
 * @method Customer[] findBy(array $criteria, array $orderBy = null, ?int $limit = null, ?int $offset = null)
 */
final class CustomerRepository extends AbstractRepository
{

	private Connection $connection;

	public function __construct(
		private \Doctrine\ORM\EntityManager $em,
		private \Doctrine\ORM\Mapping\ClassMetadata $class
	) {
		parent::__construct($em, $class);
		$this->connection = $em->getConnection();
	}

	public function getOrdersRaw(int $customerId)
	{
		$sql = '
			SELECT 
				id,
				delivery_address,
				created_at,
				status
			FROM "order" 
			WHERE customer_id = :customerId ';
		$stmt = $this->connection->prepare($sql);
		$stmt->bindValue('customerId', $customerId);
		return $stmt->executeQuery()->fetchAllAssociative();
	}

}
