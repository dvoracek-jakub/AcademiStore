<?php

declare(strict_types=1);

namespace App\Model\Product;

use App\Model\Product\Product;

class ProductPrice
{

	/** @var Product */
	private Product $product;

	public function setProduct(Product $product)
	{
	    $this->product = $product;
	}

	/**
	 * Gets price with discounts applied
	 */
	public function getPriceWithDiscounts(int $qty = 1)
	{
		$lowestPrice = $this->product->getPrice();

		// Faking
		$price = round($price * 0.9);

		$discounts = $this->product->getDiscounts();
		if (count($discounts) > 0) {
			foreach ($discounts as $discount) {

				// Is active by date?
				// @TOdo tady pořešit všechny 4 možnosti:

				// No date
				// Only FROM
				// Only TO
				// Between FROM and TO


				// Is active by quantity?
				if ($qty > 1) {

				}
			}
		}
		return $lowestPrice;
	}

	public function format($price)
	{
		return number_format((float) $price, 0, ',', ' ') . ',- Kč';
	}
}
