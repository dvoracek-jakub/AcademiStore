<?php

declare(strict_types=1);

namespace App\Module\Admin\Product;

use App\Model\Product\ProductFacade;
use Nette;
use \App\Module\Admin\Accessory\Form\ProductFormFactory;
use \Nette\Application\UI\Form;

final class ProductPresenter extends \App\Module\Admin\BasePresenter
{

	/** @var ProductFormFactory */
	private $productFormFactory;

	/** @var ProductFacade */
	private $productFacade;

	public function injectProductForm(ProductFormFactory $productFormFactory)
	{
		$this->productFormFactory = $productFormFactory;
	}

	public function injectProductFacade(ProductFacade $productFacade)
	{
		$this->productFacade = $productFacade;
	}

	public function actionList()
	{
		$this->template->products = $this->em->getRepository(\App\Model\Product\Product::class)->findAll();

		foreach ($this->template->products as $product) {
			echo $product->getUrlSlug() . "...";
			print_r($product->getCreatedAt());
			bdump($product);
		}
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

}