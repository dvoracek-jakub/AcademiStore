<?php

declare(strict_types=1);

namespace App\Module\Front\Product;

use App\Model\Product\Product;
use App\Module\Front\BasePresenter;
use Nette;

class ProductPresenter extends \App\Module\Front\BasePresenter
{

	public function actionDetail(int $id)
	{
		$product = $this->em->getRepository(Product::class)->find($id);
		$this->template->product = $product;
	}

}