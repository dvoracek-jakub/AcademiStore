<?php declare(strict_types=1);

namespace App\Model\Cart;

use App\Model\Cart\Cart;
use App\Model\Category\Category;
use App\Model\Customer\Customer;
use Nette\Application\UI\Form;

class CartService
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

	public function getCart(int $id): ?Cart
	{
		return $this->cartRepository->getCart($id);
	}

	/**
	 * @param  bool  $createNew  Create a 'new' cart if not found
	 */
	public function getCurrentCart(bool $createNew = false): ?Cart
	{
		$customerId = $this->customer ? $this->customer->getId() : null;
		$cart = $this->cartRepository->getCart(null, $customerId, 'NEW');
		if (!$cart && $createNew) {
			/** @var \App\Model\Cart\Cart */
			$cart = new Cart();

			$sessionId = $_COOKIE['PHPSESSID'] ?? null;
			if (!$customerId && !$sessionId) {
				die('ERR: Could not create new cart. No visitor identifier.'); // Řešit lépěji..
			}

			if ($customerId > 0) {
				$cart->setCustomer($this->customer);
			}

			$cart->setSessionId($sessionId);
			$cart->setStatus('NEW');
			$this->em->persist($cart);
			$this->em->flush();

			return $this->getCurrentCart();
		}

		return $cart;
	}

	public function setStatus(Cart|int $cart, string $status)
	{
		if (!$cart instanceof Cart) {
			if ((int) $cart < 1) {
				die('Neexistující košík'); // Řešit lépěji
			}
			$cart = $this->em->getRepository(Cart::class)->find($cart);
		}
		$cart->setStatus($status);
		$this->em->persist($cart);;
		$this->em->flush();
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

		$this->em->persist($cart);
		$this->em->flush();
	}

	public function getCartTotals(Cart $cart): array
	{
		$overallPrice = 0;
		foreach ($cart->getItems() as $item) {
			$overallPrice += $item->getPrice() * $item->getQuantity();
		}
		return [
			'withoutTax' => $overallPrice / 1.21, // Daň. koeficient řešit později (včetně tax-per-item)
			'withTax'    => $overallPrice,
		];
	}

}