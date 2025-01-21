<?php

declare(strict_types=1);

namespace App\Module\Front\Category;

use Nette;
use App\Model\Category\Category;

final class CategoryPresenter extends \App\Module\Front\BasePresenter
{


	public function renderDetail(int $id)
	{
		$category = $this->em->getRepository(Category::class)->find($id);
		$this->template->category = $category;
	}
}