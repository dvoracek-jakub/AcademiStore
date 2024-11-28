<?php declare(strict_types=1);

namespace App\Module\Admin\Accessory\Form;

use App\Model\Product\ProductFacade;
use \Nette\Application\UI\Form;

class ProductFormFactory
{


	public function createProductForm(): Form
	{
		$form = new Form();
		$form->addText('name', 'Název')
			->setRequired('Zadejte název');

		$form->addText('sku', 'SKU')
			->setRequired('Zadejte SKU')
			->addRule($form::MinLength, 'SKU musí obsahovat alespoň %d znaků', 5);

		$form->addTextArea('description_long', 'Dlouhý popis', 80, 18)
			->setRequired('Zadejte dlouhý popis')
			->addRule($form::MinLength, 'Dlouhý popis musí mít alespoň %d znaků', 3); // 300

		$form->addTextArea('description_short', 'Krátký popis', 80, 5)
			->addConditionOn($form['description_long'], $form::MaxLength, 1) // 100
			->setRequired('Nezadal jsi dlouhý popis, zadej krátký');

		$form->addFloat('price', 'Cena')
			->setRequired('Zadejte cenu')
			->addRule($form::Min, 'Cena musí být alespoň %d ', 1);

		/** @TODO image upload */

		$form->addSubmit('submit', 'Odeslat');
		return $form;
	}


}