<?php declare(strict_types=1);

namespace App\Model\Product;

use App\Model\Category\CategoryRepository;
use Doctrine\DBAL\Connection;
use Nettrine\Extra\Repository\AbstractRepository;
use App\Model\Category\Category;

/**
 * @extends AbstractRepository<\App\Model\Product\Product>
 * @method Product|null find($id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product|null findOneById(int $id)
 * @method Product[] findAll()
 * @method Product[] findBy(array $criteria, array $orderBy = null, ?int $limit = null, ?int $offset = null)
 */
final class ProductRepository extends AbstractRepository
{

	private Connection $connection;

	public function __construct(
		private \Doctrine\ORM\EntityManager $em,
		private \Doctrine\ORM\Mapping\ClassMetadata $class
	) {
		parent::__construct($em, $class);
		$this->connection = $em->getConnection();
	}

	public function getProducts(Category $category, int $offset, int $limit, ?array $where, ?string $order, bool $countOnly)
	{
		// Get products count only
		if ($countOnly) {
			$query = 'SELECT COUNT(p.id) FROM product p';
			if ($category instanceof Category) {
				$query .= ' INNER JOIN product_category pc ON pc.product_id = p.id AND pc.category_id = ? ';
			}
			if (!empty($where)) {
				foreach ($where as $k => $v) {
					// Converts potential camelCase to snake_case to handle the difference
					// between column naming in the database and properties in the Entity
					$snak = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $k));
					
					$query .= ' AND ' . $snak . ' ? ';
				}
			}
			$stmt = $this->em->getConnection()->prepare($query);
			if ($category instanceof Category) {
				$stmt->bindValue(1, $category->getId());
			}
			if (!empty($where)) {
				$i = 2;
				foreach ($where as $k => $v) {
					$stmt->bindValue($i, $v);
					$i++;
				}
			}
			return $stmt->executeQuery()->fetchOne();
		}

		// Get full ORM results
		$conditions = [];
		if (!empty($where)) {
			foreach ($where as $k => $v) {
				$conditions[] = $k . $v;
			}
		}
		$qb = $this->em->createQuery('
				SELECT p 
		        FROM App\Model\Product\Product p 
		        JOIN p.categories c 
		        WHERE c = :category
		        ' . (!empty($conditions) ? ' AND ' . implode(' AND ', $conditions) : '' ) . '
		        ORDER BY ' . $order . '
			')
			->setFirstResult($offset)
			->setMaxResults($limit);

		if ($category instanceof Category) {
			$qb->setParameter('category', $category);
		}
		return $qb->getResult();
	}


	/**
	 * Removes all product's category relations
	 */
	public function unwireCategories(int $productId)
	{
		$stmt = $this->connection->prepare("DELETE FROM product_category WHERE product_id = :productId");
		$stmt->bindValue('productId', $productId);
		$stmt->executeStatement();
	}

	/**
	 * Removes all product's discounts
	 */
	public function removeDiscounts(int $productId)
	{
		$stmt = $this->connection->prepare("DELETE FROM product_discount WHERE product_id = :productId");
		$stmt->bindValue('productId', $productId);
		$stmt->executeStatement();
	}

}
