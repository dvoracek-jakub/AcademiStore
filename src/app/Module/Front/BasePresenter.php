<?php

declare(strict_types=1);
namespace App\Module\Front;

use App\Model\Customer\Customer;
use App\Model\Category\Category;
use App\Model\Cart\CartService;
use App\Model\Category\CategoryService;
use App\Model\Customer\CustomerService;
use App\Model\Product\ProductService;
use App\Model\Product\ProductImage;
use Nette;

class BasePresenter extends Nette\Application\UI\Presenter
{

	/** @var CategoryService */
	protected CategoryService $categoryService;

	/** @var CartService */
	protected CartService $cartService;

	/** @var ProductService */
	protected ProductService $productService;

	/** @var CustomerService */
	protected CustomerService $customerService;

	/** @var ProductImage */
	private ProductImage $productImage;

	/** @var Customer */
	protected ?Customer $customer;

	public function __construct(
		protected \App\Model\EntityManagerDecorator $em,
		protected \App\Core\Settings $settings,
		protected \Nette\Http\Session $session,
		protected \Nette\Http\Request $httpRequest
	) {}

	public function startup()
	{
		parent::startup();
		$this->customer = null;
		if ($this->getUser()->isLoggedIn() && $this->getUser()->isInRole('customer')) {
			$this->customer = $this->em->getRepository(Customer::class)->find($this->getUser()->getId());
			if (!$this->customer || empty($this->customer)) {
				$this->flashMessage('Došlo k chybě s přihlašováním.');
				$this->getUser()->logout();
				$this->redirect('Home:');
			}
		}
	}

	public function injectCategoryService(CategoryService $categoryService)
	{
		$this->categoryService = $categoryService;
	}

	public function injectCartService(CartService $cartService)
	{
		$this->cartService = $cartService;
	}

	public function injectProductService(ProductService $productService)
	{
		$this->productService = $productService;
	}

	public function injectProductImage(ProductImage $productImage)
	{
		$this->productImage = $productImage;
	}

	public function injectCustomerService(CustomerService $customerService)
	{
		$this->customerService = $customerService;
	}

	public function beforeRender()
	{
		$tpl = $this->getTemplate();
		$tpl->settings = $this->settings;

		$tpl->categoryTree = $this->em->getRepository(Category::class)->getForTreeView();
	}

	public function shutdown(\Nette\Application\Response $response): void
	{
		$request = $this->getHttpRequest();
		$system = $this->session->getSection('system');
		$system->lastVisitedPage = $request->getUrl();
	}

}