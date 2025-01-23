<?php

declare(strict_types=1);

namespace App\Module\Admin\Product;

use App\Model\Category\Category;
use App\Model\Product\ProductFacade;
use Nette;
use App\Module\Admin\Accessory\Form\ProductFormFactory;
use Nette\Application\UI\Form;
use Contributte\Datagrid\Datagrid;
use App\Model\Product\ProductImage;


final class ProductPresenter extends \App\Module\Admin\BasePresenter
{

	/** @var \App\Model\Product\Product */
	private $product;

	/** @var ProductFormFactory */
	private $productFormFactory;

	/** @var ProductFacade */
	private $productFacade;

	/** @var ProductImage */
	private $productImage;

	/** @var \App\Model\Product\ProductRepository */
	private $productRepository;

	public function injectProductForm(ProductFormFactory $productFormFactory)
	{
		$this->productFormFactory = $productFormFactory;
		$this->productRepository = $this->em->getRepository(\App\Model\Product\Product::class);
	}

	public function injectProductImage(ProductImage $productImage)
	{
		$this->productImage = $productImage;
	}

	public function injectProductFacade(ProductFacade $productFacade)
	{
		$this->productFacade = $productFacade;
	}

	public function actionCreate()
	{
		$tpl = $this->getTemplate();
		$tpl->action = 'create';
		$tpl->categoryTree = $this->em->getRepository(Category::class)->getForTreeView();
	}

	public function actionEdit(int $id)
	{
		$this->product = $this->productRepository->findOneById($id);

		// Fetch products categories
		$relatedCategories = [];
		foreach ($this->product->getCategories() as $category) {
			$relatedCategories[] = $category->getId();
		}

		if (!$this->product) {
			throw new \Exception('Product not found');
		}
		
		$tpl = $this->getTemplate();
		$tpl->setFile(__DIR__ . '/create.latte');
		$tpl->action = 'edit';
		$tpl->categoryTree = $this->em->getRepository(Category::class)->getForTreeView();
		$tpl->relatedCategories = $relatedCategories;
		$tpl->product = $this->product;
		$tpl->imageName = '';
		if (!empty($this->product->getImageName())) {
			$tpl->imageName = $this->productImage->getImage(
				$this->product,
				$this->settings->store->product_image_medium
			);
		}
	}

	public function createComponentProductForm(): Form
	{
		$form = $this->productFormFactory->createProductForm($this->action, $this->product ?? null);

		$form->onSuccess[] = function(): void {
			$this->flashMessage('Produkt byl úspěšně uložen', 'success');

			if ($this->action == 'create') {
				$lastProduct = $this->productRepository->findBy([], ['id' => 'DESC'], 1);
				$this->redirect('Product:edit', $lastProduct[0]->getId());
			} else {
				$this->redirect('this');
			}
		};
		return $form;
	}

	public function createComponentProductsDatagrid($name)
	{
		$grid = new Datagrid($this, $name);
		$grid->setTemplateFile(__DIR__ . '/../datagrid_override.latte');
		$settings = $this->settings->store;

		$queryBuilder = $this->productRepository->createQueryBuilder('p')
			->select('p', 'c')
			->leftJoin('p.categories', 'c');
		$grid->setDataSource($queryBuilder);
		$grid->setDefaultSort(['name' => 'ASC']);
		$grid->setDefaultPerPage(20);

		$grid->addColumnText('id', 'ID');

		$grid->addColumnText('image', ' ')->setRenderer(function($row) use ($settings) {
			echo '<img src="' . $row->getImage($settings->product_image_small) . '" width="25" data-full="' . $row->getImage($settings->product_image_medium) . '">';
		});

		$grid->addColumnLink('name', 'Name', 'edit')
			->setClass('block hover:text-pink-600')
			->setSortable();


		$grid->addColumnText('url', 'URL', 'url_slug')->setRenderer(function($row) {
			return '/' . $row->getUrlSlug();
		});

		$grid->addColumnText('categories', 'Kategorie')->setRenderer(function($product) {
			$cats = $product->getCategories();
			if (!empty($cats)) {
				$catArray = [];
				foreach ($cats as $cat) {
					$catArray[] = $cat->getName();
				}
				echo implode(', ', $catArray);
			}
			return;
		});

		$grid->addFilterSelect('categories', '..', $this->categoryFacade->getAssociative())
			->setAttribute('class', 'select2')
			->setCondition(function($queryBuilder, $value) {
				$queryBuilder->where('c.id = :catId')->setParameter('catId', $value);
			});

		$grid->addColumnNumber('price', 'Cena')->setSortable()
			->setFilterRange('price', 'x')->setPlaceholders(['Od', 'Do']);

		$grid->addColumnText('active', 'Aktivní')->setRenderer(function($row) {
			return $row->getActive() == 1 ? '✔️' : '❌';
		});

		$grid->addFilterText('name', 'Name')->setPlaceholder('Hledat dle názvu');
	}

}