<?php

declare(strict_types=1);
namespace App\Module\Front;

use App\Model\Category\Category;
use App\Model\Category\CategoryFacade;
use App\Model\Product\ProductFacade;
use App\Model\Product\ProductImage;
use Nette;

class BasePresenter extends Nette\Application\UI\Presenter
{

	/** @var CategoryFacade */
	protected $categoryFacade;

	/** @var ProductFacade */
	protected $productFacade;

	/** @var ProductImage */
	private $productImage;

	public function injectCategoryFacade(CategoryFacade $categoryFacade)
	{
		$this->categoryFacade = $categoryFacade;
	}

	public function injectProductFacade(ProductFacade $productFacade)
	{
		$this->productFacade = $productFacade;
	}

	public function injectProductImage(ProductImage $productImage)
	{
		$this->productImage = $productImage;
	}

	public function __construct(
		protected \App\Model\EntityManagerDecorator $em,
		protected \App\Core\Settings $settings
	) {}

	public function startup()
	{
		parent::startup();
	}

	public function beforeRender()
	{
		$tpl = $this->getTemplate();
		$tpl->settings = $this->settings;
		$tpl->categoryTree = $this->em->getRepository(Category::class)->getForTreeView();
	}

}