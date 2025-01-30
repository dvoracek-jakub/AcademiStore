<?php

declare(strict_types=1);

namespace App\Module\Front\Accessory;

use Latte\Extension;


final class LatteExtension extends Extension
{

	public function __construct(private \App\Model\Product\ProductPrice $productPrice) {}

	public function getFilters(): array
	{
		return [
			'paragraphize' => [$this, 'filterParagraphize'],
			'formatPrice'  => [$this, 'filterFormatPrice'],
		];
	}

	public function getFunctions(): array
	{
		return [];
	}

	public function filterParagraphize(string $source)
	{
		return '<p>' . implode('</p> <p>', array_map('trim', explode("\n", $source))) . '</p>';
	}

	public function filterFormatPrice($price)
	{
		return $this->productPrice->format($price);
	}

}
