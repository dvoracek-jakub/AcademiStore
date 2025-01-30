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
	public function getPriceWithDiscounts(int $orderedQty = 1)
	{
		$lowestPrice = $this->product->getPrice();

		$discounts = $this->product->getDiscounts();
		if (count($discounts) > 0) {
			foreach ($discounts as $discount) {
				$isActive = true;
				$todayDate = new \DateTime();

				if ($discount->getStartDate() && $discount->getStartDate() > $todayDate) {
					$isActive = false;
				}

				if ($discount->getEndDate() && $discount->getEndDate() < $todayDate) {
					$isActive = false;
				}

				// Is active by quantity in the cart?
				if ($orderedQty > 1 && $orderedQty < $discount->getFromQuantity()) {
					$isActive = false;
				}

				if ($isActive && $discount->getPrice() < $lowestPrice) {
					$lowestPrice = $discount->getPrice();
				}
			}
		}
		return $lowestPrice;
	}

	public function format($price)
	{
		// CZ
		return number_format((float) $price, 0, ',', ' ') . ',- Kč';
	}

}
