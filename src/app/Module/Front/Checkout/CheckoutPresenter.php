<?php

declare(strict_types=1);

namespace App\Module\Front\Checkout;

use App\Model\Shipping\ShippingFacade;
use Nette\Application\UI\Form;
use App\Module\Front\Accessory\Form\CheckoutFormFactory;

class CheckoutPresenter extends \App\Module\Front\BasePresenter
{

	/** @var CheckoutFormFactory */
	private CheckoutFormFactory $checkoutFormFactory;

	public function injectCheckoutForm(CheckoutFormFactory $checkoutFormFactory)
	{
		$this->checkoutFormFactory = $checkoutFormFactory;
	}

	public function actionOverview()
	{
		$cart = $this->cartFacade->getCurrentCart();

		if (!$cart) {
			$this->flashMessage('V košíku nejsou žádné položky');
			$this->redirect('Home:');
		}

		$cartTotals = $this->cartFacade->getCartTotals($cart);
		// todo tady zjisti price za shipping
		// todo tady zjisti price za paymentc
		// todo tady sečti všechny ceny do totals

		$this->template->cart = $cart;
	}

	public function createComponentCheckoutForm()
	{
		$form = $this->checkoutFormFactory->createCheckoutForm();
		$form->onSuccess[] = function(): void {
			// Tady asi nic, různé redirecty na platební brány apod se budou dít v checkoutformfactory
		};
		return $form;
	}

}