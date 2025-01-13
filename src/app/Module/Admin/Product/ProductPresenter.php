<?php

declare(strict_types=1);

namespace App\Module\Admin\Product;

use App\Model\Product\ProductFacade;
use Nette;
use \App\Module\Admin\Accessory\Form\ProductFormFactory;
use \Nette\Application\UI\Form;
use \Contributte\Datagrid\Datagrid;

final class ProductPresenter extends \App\Module\Admin\BasePresenter
{

	/** @var ProductFormFactory */
	private $productFormFactory;

	/** @var ProductFacade */
	private $productFacade;

	/** @var \App\Model\Product\ProductRepository */
	private $productRepository;

	public function injectProductForm(ProductFormFactory $productFormFactory)
	{
		$this->productFormFactory = $productFormFactory;
		$this->productRepository = $this->em->getRepository(\App\Model\Product\Product::class);
	}

	public function injectProductFacade(ProductFacade $productFacade)
	{
		$this->productFacade = $productFacade;
	}

	public function actionList()
	{
		// ORM STYLE
		/*$this->template->products = $this->productRepository->findAll();
		foreach ($this->template->products as $product) {
			//echo $product->getUrlSlug() . "...";
			//print_r($product->getCreatedAt());
			//bdump($product);
		}

		// DBAL PS
		$this->template->products = $this->productRepository->getProducts();
		foreach ($this->template->products as $product) {
			//print_r($product);
			//print_r($product->getCreatedAt());
			//bdump($product);
		}*/
	}

	public function actionCreate() {}

	public function createComponentProductForm(): Form
	{
		$form = $this->productFormFactory->createProductForm();
		$form->onSuccess[] = function(): void {
			$this->flashMessage('Produkt byl úspěšně uložen.');
			$this->redirect('this');
		};
		return $form;
	}

	public function createComponentProductsList($name)
	{
		$grid = new Datagrid($this, $name);

		//$grid->setDataSource($this->productRepository->getProducts());
		$grid->setDataSource($this->productRepository->createQueryBuilder('p'));
		$grid->setDefaultSort(['name' => 'ASC']);
		$grid->setDefaultPerPage(20);

		$grid->addColumnLink('name', 'Name', 'edit')->setSortable();

		$grid->addColumnText('url', 'URL', 'url_slug')->setRenderer(function($item) {
			return '/' . $item->getUrlSlug();
		});

		$grid->addColumnNumber('price', 'Cena')->setSortable()
			->setFilterRange('price', 'dfs')->setPlaceholders(['Cena od', 'Cena do']);

		$grid->addColumnText('active', 'Aktivní')->setRenderer(function($item) {
			return $item->getActive() == 1 ? '✔️' : '❌';
		});

		$grid->addFilterText('name', 'Name')->setPlaceholder('Hledat dle názvu');
	}

}