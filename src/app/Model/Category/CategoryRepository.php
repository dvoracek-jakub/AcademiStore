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

	public function findProductsByCategory(Category $category, int $offset, int $limit): array
	{
		return $this->em->createQuery('
				SELECT p 
		        FROM App\Model\Product\Product p 
		        JOIN p.categories c 
		        WHERE c = :category
			')
			->setParameter('category', $category)
			->setFirstResult($offset)
			->setMaxResults($limit)
			->getResult();
	}

	public function getForListDatagrid()
	{
		$stmt = $this->connection->prepare("
			 WITH RECURSIVE category_hierarchy AS (
			    SELECT
			        id,
			        name,
			        parent_id,
			        active,
			        '[' ||  id  || ']' || name::text AS full_path
			    FROM category
			    WHERE parent_id IS NULL
			    
			    UNION ALL
			    
			    SELECT
			        c.id,
			        c.name,
			        c.parent_id,
			        c.active,
			        (  ch.full_path || ' > ' || '[' || c.id || ']' || c.name)::text
			    FROM category c
			    INNER JOIN category_hierarchy ch ON ch.id = c.parent_id
			)
			SELECT
			    c.id,
			    c.name,
			    c.parent_id,
			    c.active,
			    ch.full_path AS parents_name
			FROM category c
			LEFT JOIN category_hierarchy ch ON c.id = ch.id  AND c.parent_id IS NOT NULL;
		 ");
		return $stmt->executeQuery()->fetchAllAssociative();
	}

	public function getForTreeView()
	{
		$stmt = $this->connection->prepare("
			 WITH RECURSIVE category_hierarchy AS (
				SELECT
					id,
					name,
					parent_id,
					1 AS level,
					name::text AS full_path
				FROM category
				WHERE parent_id IS NULL
				
				UNION ALL
			
				SELECT
				c.id,
				c.name,
				c.parent_id,
				ch.level + 1 AS level,
				ch.full_path || ' > ' || c.name AS full_path
				FROM category c
				INNER JOIN category_hierarchy ch ON c.parent_id = ch.id
			)
			SELECT
				id,
				name,
				parent_id,
				level,
				full_path
			FROM category_hierarchy
			ORDER BY full_path;
		 ");
		return $stmt->executeQuery()->fetchAllAssociative();
	}

}
