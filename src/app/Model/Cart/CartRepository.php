<?php declare(strict_types=1);

namespace App\Model\Cart;

use Doctrine\DBAL\Connection;
use http\Exception\InvalidArgumentException;
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

	/**
	 * Gets cart by id or the visitor's "new" one
	 */
	public function getCart(?int $cartId = null, ?int $customerId = null, ?string $status = null): ?Cart
	{
		$r = $this->createQueryBuilder('c');
		$sessionId = $_COOKIE['PHPSESSID'] ?? null;

		if ($cartId) {
			$r->where('c.id = :cartId')
				->setParameter('cartId', $cartId);
		} else {
			if ($customerId) {
				$r->where('c.customerId = :customerId')
					->setParameter('customerId', $customerId);
			} else {
				if ($sessionId && !empty($sessionId)) {
					$r->where('c.sessionId = :sessionId')
						->setParameter('sessionId', $sessionId);
				} else {
					return null;
				}
			}
		}

		if ($status) {
			$r->andWhere('c.status = :status')
				->setParameter('status', $status);
		}

		$result = $r->getQuery()->getResult();
		return (!empty($result) ? $result[0] : null);
	}

}
