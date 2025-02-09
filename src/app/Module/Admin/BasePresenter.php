<?php

declare(strict_types=1);
namespace App\Module\Admin;

use App\Model\Category\CategoryService;
use Nette;

class BasePresenter extends Nette\Application\UI\Presenter
{

	/** @var CategoryService */
	protected $categoryService;

	public function __construct(
		protected \App\Model\EntityManagerDecorator $em,
		protected \App\Core\Settings $settings
	) {}

	public function startup()
	{
		parent::startup();
		if (!$this->getUser()->isLoggedIn() || !$this->getUser()->isInRole('admin')) {
			$this->redirect('Sign:in');
		}
	}

	public function beforeRender()
	{
		$this->template->product_image_path = $this->settings->store->product_image_path;
	}

	public function injectCategoryService(CategoryService $categoryService)
	{
		$this->categoryService = $categoryService;
	}

}