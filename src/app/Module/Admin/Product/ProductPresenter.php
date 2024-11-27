<?php

declare(strict_types=1);

namespace App\Module\Admin\Product;

use Nette;

final class ProductPresenter extends \App\Module\Admin\BasePresenter
{

	/** @var \App\Module\Admin\Accessory\Form\ProductForm */
	private $productForm;

	public function injectProductForm(\App\Module\Admin\Accessory\Form\ProductForm $productForm) {
		$this->productForm = $productForm;
	}

	public function actionList()
	{
		$this->template->products = $this->em->getRepository(\App\Model\Product\Product ::class)->findAll();

		foreach ($this->template->products as $product) {
			echo $product->getUrlSlug() . "...";
			print_r($product->getCreatedAt());
			bdump($product->getCreatedAt());
		}
	}

	public function actionCreate()
	{

	}

	public function createComponentProductForm()
	{
		return $this->productForm->createProductForm();
	}

}