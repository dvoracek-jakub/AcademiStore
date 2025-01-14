<?php

declare(strict_types=1);

namespace App\Module\Admin\Product;

use App\Model\Product\ProductFacade;
use Nette;
use \App\Module\Admin\Accessory\Form\ProductFormFactory;
use \Nette\Application\UI\Form;
use \Contributte\Datagrid\Datagrid;
use App\Model\Product\ProductImage;
use function Symfony\Component\String\b;

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

	public function actionCreate()
	{
		$this->template->action = 'create';
	}

	public function actionEdit(int $id)
	{
		$this->product = $this->productRepository->findOneById($id);

		if (!$this->product) {
			throw new \Exception('Product not found');
		}

		$tpl = $this->getTemplate();
		$tpl->setFile(__DIR__ . '/create.latte');
		$tpl->action = 'edit';

		$tpl->imageName = '';
		if (!empty($this->product->getImageName())) {
			$tpl->imageName = $this->productImage->getImage($this->product->getImageName(), $this->settings->store->product_image_medium);
		}
	}

	public function createComponentProductForm(): Form
	{
		$form = $this->productFormFactory->createProductForm($this->action, $this->product ? $this->product->getId() : null);

		if ($this->action == 'edit') {
			//$form->setDefaults($product); ⛔
			$form['name']->setDefaultValue($this->product->getName());
			$form['sku']->setDefaultValue($this->product->getSku());
			$form['price']->setDefaultValue($this->product->getPrice());
			$form['descShort']->setDefaultValue($this->product->getDescShort());
			$form['descLong']->setDefaultValue($this->product->getDescLong());
		}

		$form->onSuccess[] = function(): void {
			$this->flashMessage('Produkt byl úspěšně uložen.');

			if ($this->action == 'create') {
				// @TODO Get last product id and redirect there
				$lastProduct = $this->productRepository->findBy([], ['id' => 'DESC'], 1);
				$this->redirect('Product:edit', $lastProduct[0]->getId());
				exit;
			} else {
				$this->redirect('this');
			}

		};

		return $form;
	}

	public function createComponentProductsList($name)
	{
		$grid = new Datagrid($this, $name);
		$grid->setTemplateFile(__DIR__ . '/../datagrid_override.latte');

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