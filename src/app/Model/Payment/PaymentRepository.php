<?php declare(strict_types=1);

namespace App\Model\Payment;

use App\Model\Payment\Payment;
use Doctrine\DBAL\Connection;
use Nettrine\Extra\Repository\AbstractRepository;

/**
 * @extends AbstractRepository<\App\Model\Payment\Payment>
 * @method Customer|null find($id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer|null findOneById(int $id)
 * @method Customer[] findAll()
 * @method Customer[] findBy(array $criteria, array $orderBy = null, ?int $limit = null, ?int $offset = null)
 */
final class PaymentRepository extends AbstractRepository
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
