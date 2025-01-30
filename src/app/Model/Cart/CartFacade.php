<?php declare(strict_types=1);

namespace App\Model\Cart;

use App\Model\Cart\Cart;
use App\Model\Category\Category;
use App\Model\Customer\Customer;
use Nette\Application\UI\Form;

class CartFacade
{

	/** @var \App\Model\Cart\CartRepository|\Doctrine\ORM\EntityRepository */
	private $cartRepository;

	public function __construct(
		public \App\Model\EntityManagerDecorator $em
	) {
		$this->cartRepository = $this->em->getRepository(Cart::class);
	}

	/**
	 * @param  bool  $createNew  Create a 'new' cart if not found
	 */
	public function getCurrentCart(bool $createNew = false, ?Customer $customer = null): ?\App\Model\Cart\Cart
	{
		$customerId = $customer ? $customer->getId() : null;
		$cart = $this->em->getRepository(\App\Model\Cart\Cart::class)->getCart(null, $customerId, 'new');
		if (!$cart && $createNew) {
			/** @var \App\Model\Cart\Cart */
			$cart = new \App\Model\Cart\Cart();

			$sessionId = $_COOKIE['PHPSESSID'] ?? null;
			if (!$customerId && !$sessionId) {
				die('ERR: Could not create new cart. No visitor identifier.'); // Řešit lépěji..
			}

			if ($customerId > 0) {
				$customer = $this->em->getRepository(Customer::class)->find($customerId);
				$cart->setCustomer($customer);
			}

			$cart->setSessionId($sessionId);
			$cart->setStatus('new');
			$this->em->persist($cart);
			$this->em->flush();

			return $this->getCurrentCart(false, $customer);
		}
		return $cart;
	}

	public function cartItemExists(Cart $cart, int $productId, int $cartItemId = null)
	{
		$cartItems = $cart->getItems();
		if (empty($cartItems)) {
			return false;
		}
		foreach ($cartItems as $item) {
			if ($productId == $item->getProduct()->getId()) {
				return true;
			}
		}
		return false;
	}

	public function getCartTotals(Cart $cart): array
	{
		$overallPrice = 0;
		foreach ($cart->getItems() as $item) {
			$overallPrice += $item->getPrice() * $item->getQuantity();
		}
		return [
			'withoutTax' => $overallPrice / 1.21, // Daň. koeficient řešit později (včetně tax-per-item)
			'withTax' => $overallPrice,
		];
	}

}