<?php declare(strict_types=1);

namespace App\Model\Category;

use App\Model\Category\Category;
use Nette\Application\UI\Form;

class CategoryService
{

	/** @var \App\Model\Category\CategoryRepository|\Doctrine\ORM\EntityRepository */
	private $categoryRepository;

	public function __construct(private \App\Model\EntityManagerDecorator $em)
	{
		$this->categoryRepository = $this->em->getRepository(Category::class);
	}

	public function saveCategory($data, int $id = null): Category
	{
		if ($id) {
			$category = $this->categoryRepository->findOneById($id);
		} else {
			$category = new Category();
		}

		if ((int) $data->parentId > 0) {
			$category->setParent($this->categoryRepository->findOneById($data->parentId));
		} else {
			$category->setParent(null);
		}

		$category->setName($data->name);
		$category->setDescription($data->description);
		$category->setActive($data->active);
		$category->setUrlSlug($this->generateUrlSlug($data->name));

		$this->em->persist($category);
		$this->em->flush();
		return $category;
	}

	public function generateUrlSlug(string $name): string
	{
		$urlSlug = \Nette\Utils\Strings::webalize($name);

		// Check if the slug already exists. If so, modify it a bit.
		$category = $this->categoryRepository->findBy(['urlSlug' => $urlSlug]);
		if (isset($category[0])) {
			$urlSlug = $this->generateUrlSlug($name . '-' . rand(1, 999));
		}
		return $urlSlug;
	}

	public function getAssociative()
	{
		$categories = $this->categoryRepository->findAll();
		$cats = [null => '---'];
		if (!empty($categories)) {
			foreach ($categories as $cat) {
				$cats[$cat->getId()] = $cat->getName();
			}
		}
		return $cats;
	}

	public function delete(int $id)
	{
	    $category = $this->categoryRepository->findOneById($id);
		$this->em->remove($category);
		$this->em->flush();
	}

}