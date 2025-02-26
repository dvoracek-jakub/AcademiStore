<?php

declare(strict_types=1);

namespace App\Module\Front\Category;

use Nette;
use App\Model\Category\Category;
use App\Model\Category\CategoryRepository;
use App\Module\Front\Accessory\Form\ProductFilterFormFactory;

final class CategoryPresenter extends \App\Module\Front\BasePresenter
{

	private $categoryRepository;

	/** @var ProductFilterFormFactory */
	private ProductFilterFormFactory $productFilterFormFactory;

	public function injectProductFilterForm(ProductFilterFormFactory $productFilterFormFactory)
	{
		$this->productFilterFormFactory = $productFilterFormFactory;
	}

	public function renderDetail(int $id, int $page = 1)
	{
		$category = $this->em->getRepository(Category::class)->find($id);
		if (!$category) {
			$this->error('Kategorie nenalezena.');
		}

		// Paging
		$paginator = new \Nette\Utils\Paginator();
		$paginator->setItemsPerPage(16);
		$paginator->setPage($page);
		$totalProducts = count($category->getProducts());
		$paginator->setItemCount($totalProducts);



		// Filter todo: nekam presunout(?)
		$filter = [];
		if ($this->getParameter('order')) {
			$filter['order'] = $this->getParameter('order');
		}

		if ($this->getParameter('priceFrom')) {
			$filter['priceFrom'] = $this->getParameter('priceFrom');
		}

		if ($this->getParameter('priceTo')) {
			$filter['priceTo'] = $this->getParameter('priceTo');
		}

		print_r($filter); die(" |Line: " . __LINE__);

		$products = $this->categoryService->findProductsByCategory($category, $paginator->getOffset(), $paginator->getLength(), $filter);
		$this->template->products = $products;
		$this->template->category = $category;
		$this->template->paginator = $paginator;
		$this->template->categoryId = $id;
	}

	protected function createComponentFilterForm()
	{
		$form = $this->productFilterFormFactory->create();

		$form->setDefaults([
			'order'     => $this->getParameter('order'),
			'priceFrom' => $this->getParameter('priceFrom'),
			'priceTo'   => $this->getParameter('priceTo'),
		]);

		$form->onSuccess[] = function($form, $values) {
			$this->redirect('this', (array) $values);
		};

		return $form;
	}

}