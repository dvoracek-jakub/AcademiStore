<?php declare(strict_types=1);

namespace App\Model\Delivery\Shipping;

use App\Model\Shipping\Customer;
use Doctrine\DBAL\Connection;
use Nettrine\Extra\Repository\AbstractRepository;

/**
 * @extends AbstractRepository<\App\Model\Delivery\Shipping\Shipping>
 * @method Customer|null find($id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer|null findOneById(int $id)
 * @method Customer[] findAll()
 * @method Customer[] findBy(array $criteria, array $orderBy = null, ?int $limit = null, ?int $offset = null)
 */
final class ShippingRepository extends AbstractRepository
{

	private Connection $connection;

	/** TODO Posunout (s ostatními konstuktory) výše do nově vytvořené custom AbstractRepository ? */
	public function __construct(
		private \Doctrine\ORM\EntityManager $em,
		private \Doctrine\ORM\Mapping\ClassMetadata $class
	) {
		parent::__construct($em, $class);
		$this->connection = $em->getConnection();
	}

}
