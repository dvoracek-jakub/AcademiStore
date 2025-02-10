<?php

declare(strict_types=1);

namespace App\Module\Front\Cart;

use App\Model\Product\Product;
use App\Model\Cart\CartItem;

final class CartPresenter extends \App\Module\Front\BasePresenter
{

	public function startup()
	{
		parent::startup();
		$this->cartService->setCustomer($this->customer);
	}

	// Musi byt render* metoda, aby reflektovala zmeny z handle* signalu. Action* se totiz vola pred nim.
	public function renderDetail()
	{
		$cart = $this->cartService->getCurrentCart();

		if (!$cart) {
			$this->flashMessage('V košíku nejsou žádné položky');
			$this->redirect('Home:');
		}

		$this->template->cart = $cart;
		$this->template->edit = true;
		$this->template->cartTotals = $this->cartService->getCartTotals($cart);
	}

	public function handleUpdateQuantity()
	{
		$itemId = (int) $_POST['itemId'];
		$quantity = (int) $_POST['quantity'];
		$this->cartService->updateItemQuantity($itemId, $quantity);

	    $this->redrawControl('cartItems');
	}

	public function actionAdd(int $productId, int $quantity = 1)
	{
		/** @var \App\Model\Cart\Cart */
		$cart = $this->cartService->getCurrentCart(true);

		$product = $this->em->getRepository(Product::class)->find($productId);
		if (!$product) {
			die('ERR: Product not found.');  // Řešit lépěji...
		}

		if ($this->cartService->cartItemExists($cart, $productId)) {
			$cartItem = $this->em->getRepository(CartItem::class)->findOneBy([
				'cart'    => $cart->getId(),
				'product' => $productId,
			]);
			$cartItem->setQuantity($quantity + $cartItem->getQuantity());
			$this->em->persist($cartItem);
			$this->em->flush();
		} else {
			$item = new \App\Model\Cart\CartItem();
			$item->setProduct($product);
			$item->setQuantity($quantity);
			$item->setPrice($product->getPriceWithDiscounts());
			$cart->addItem($item);
			$this->em->persist($cart);
			$this->em->flush();
		}

		if ($this->isAjax()) {
			die(" vratit JSON...");
		} else {
			$system = $this->session->getSection('system');
			$this->redirectUrl((string) $system->lastVisitedPage);
		}
	}

	public function handleRemoveItem(int $itemId)
	{
		$cartItem = $this->em->getRepository(CartItem::class)->find($itemId);
		if (!$cartItem) {
			die('ERR: Product not found in cart'); // Řešit lépěji...
		}
		$this->em->remove($cartItem);
		$this->em->flush();

		if ($this->isAjax()) {
			$this->redrawControl('cartItems');
		} else {
			$this->redirect('this');
		}
	}

}