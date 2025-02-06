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

	/** @var Customer */
	private $customer = null;

	public function __construct(
		public \App\Model\EntityManagerDecorator $em
	) {
		$this->cartRepository = $this->em->getRepository(Cart::class);
	}

	public function setCustomer(Customer $customer = null)
	{
	    $this->customer = $customer;
	}

	/**
	 * @param  bool  $createNew  Create a 'new' cart if not found
	 */
	public function getCurrentCart(bool $createNew = false): ?\App\Model\Cart\Cart
	{
		$customerId = $this->customer ? $this->customer->getId() : null;
		$cart = $this->em->getRepository(\App\Model\Cart\Cart::class)->getCart(null, $customerId, 'new');
		if (!$cart && $createNew) {
			/** @var \App\Model\Cart\Cart */
			$cart = new \App\Model\Cart\Cart();

			$sessionId = $_COOKIE['PHPSESSID'] ?? null;
			if (!$customerId && !$sessionId) {
				die('ERR: Could not create new cart. No visitor identifier.'); // Řešit lépěji..
			}

			if ($customerId > 0) {
				$cart->setCustomer($this->customer);
			}

			$cart->setSessionId($sessionId);
			$cart->setStatus('new');
			$this->em->persist($cart);
			$this->em->flush();

			return $this->getCurrentCart();
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

	public function updateItemQuantity(int $itemId, int $quantity)
	{
		$cart = $this->getCurrentCart();
		$cartItems = $cart->getItems();
		if (!empty($cartItems)) {
			foreach ($cartItems as $item) {
				if ($itemId == $item->getId()) {
					if ($quantity > 0) {
						$item->setQuantity($quantity);
						$this->em->persist($item);
						$this->em->flush();
					} else {
						$this->em->remove($item);
						$this->em->flush();
					}
				}
				$this->em->refresh($item);
			}
		}

		//@TODO totals, tady jeste ok..
		foreach ($cart->getItems() as $item) {
			bdump($item->getQuantity(),  "UIQ QTY for ITEM: " . $item->getProduct()->getName());
		}
		//$this->em->refresh($cart);
		$this->em->persist($cart);
		$this->em->flush();
	}

	public function getCartTotals(Cart $cart): array
	{
		$overallPrice = 0;

		//@TODO totals, tady ale stare hodnoty.. pritom radky v latte vypisuje katualni
		foreach ($cart->getItems() as $item) {
			bdump($item->getQuantity(),  "QTY for ITEM: " . $item->getProduct()->getName());
			$overallPrice += $item->getPrice() * $item->getQuantity();
		}
		return [
			'withoutTax' => $overallPrice / 1.21, // Daň. koeficient řešit později (včetně tax-per-item)
			'withTax' => $overallPrice,
		];
	}

}