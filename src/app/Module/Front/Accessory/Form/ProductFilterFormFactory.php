<?php

namespace App\Module\Front\Accessory\Form;


use Nette\Application\UI\Form;

class ProductFilterFormFactory
{
	public function create(): Form
	{
		$form = new Form;

		// Order By
		$form->addSelect('order', 'Řadit podle:', [
			'price_asc' => 'Cena: od nejlevnějšího',
			'price_desc' => 'Cena: od nejdražšího',
			'name_asc' => 'Název: A-Z',
			'name_desc' => 'Název: Z-A',
		])->setPrompt('Vyberte řazení');

		// Filters
		$form->addText('priceFrom', 'Cena od:')
			->setHtmlType('number');
		$form->addText('priceTo', 'Cena do:')
			->setHtmlType('number');

		$form->addSubmit('submit', 'Filtrovat');
		return $form;
	}
}