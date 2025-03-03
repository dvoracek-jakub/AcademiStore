<?php

declare(strict_types=1);

namespace App\Module\Front\Category;

use Nette;
use App\Model\Category\Category;
use App\Model\Category\CategoryRepository;
use App\Module\Front\Accessory\Form\ProductFilterFormFactory;

final class CategoryPresenter extends \App\Module\Front\BasePresenter
{

	/** @persistent */
	public string $order;

	/** @persistent */
	public string $priceFrom;

	/** @persistent */
	public string $priceTo;

	private $categoryRepository;

	/** @var ProductFilterFormFactory */
	private ProductFilterFormFactory $productFilterFormFactory;

	public function injectProductFilterForm(ProductFilterFormFactory $productFilterFormFactory)
	{
		$this->productFilterFormFactory = $productFilterFormFactory;
	}

	public function renderDetail(int $id, int $page = 1, $order = '', $priceFrom = '', $priceTo = '')
	{
		$category = $this->em->getRepository(Category::class)->find($id);
		if (!$category) {
			$this->error('Kategorie nenalezena.');
		}

		// Filter
		$filter = [];
		$filter['order'] = $order;

		if ((int) $priceFrom > 0) {
			$filter['priceFrom'] = (int) $priceFrom;
		}

		if ((int) $priceTo > 0) {
			$filter['priceTo'] = (int) $priceTo;
		}

		// Paging
		$paginator = new \Nette\Utils\Paginator();
		$paginator->setItemsPerPage(16);
		$paginator->setPage($page);
		$totalProducts = $this->productService->getProducts($category, $paginator->getOffset(), $paginator->getLength(), $filter, true);
		$paginator->setItemCount($totalProducts);

		$products = $this->productService->getProducts($category, $paginator->getOffset(), $paginator->getLength(), $filter);
		$this->template->products = $products;
		$this->template->category = $category;
		$this->template->paginator = $paginator;
		$this->template->categoryId = $id;
		$this->template->order = $order;
		$this->template->priceFrom = $priceFrom;
		$this->template->priceTo = $priceTo;
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