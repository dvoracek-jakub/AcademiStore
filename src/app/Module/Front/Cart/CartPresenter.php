<?php

declare(strict_types=1);

namespace App\Module\Front\Cart;

final class CartPresenter extends \App\Module\Front\BasePresenter
{

	public function actionDetail()
	{
	    $cart = $this->em->getRepository(\App\Model\Cart\Cart::class)->find(1);
		bdump($cart);
	}

	public function actionAdd(int $productId)
	{
		// @ TODO Check, zda customer ma uz kosik (a taky kdo je customer (prihlasen, ci podle session_id?)
		// Pokud nema, tak vytvorit novy, get its ID

		// Pridat product do daneho cart
		if ($this->isAjax()) {
			die(" vratit JSON...");
		} else {
			$system = $this->session->getSection('system');
			$this->redirectUrl((string) $system->lastVisitedPage); // @TODO redirect zpet na predchozi stranku
		}

	}

}