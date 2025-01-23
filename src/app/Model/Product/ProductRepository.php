<?php declare(strict_types=1);

namespace App\Model\Product;

use Doctrine\DBAL\Connection;
use Nettrine\Extra\Repository\AbstractRepository;

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

	public function getProducts()
	{
		$sql = "SELECT * FROM product";
		$stmt = $this->connection->prepare($sql);
		return $stmt->executeQuery()->fetchAllAssociative();   // fetchAssociative() ...
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
