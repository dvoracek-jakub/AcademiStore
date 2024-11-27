<?php declare(strict_types=1);

namespace App\Module\Admin\Accessory\Form;

use \Nette\Application\UI\Form;

class ProductForm
{

	public function createProductForm(): Form
	{
		$form = new Form();
		$form->addText('name', 'Název')
			->setRequired('Zadejte název');

		$form->addTextArea('description_long', 'Dlouhý popis', 80, 18)
			->setRequired('Zadejte dlouhý popis')
			->addRule($form::MinLength, 'Dlouhý popis musí mít alespoň %d znaků', 300);

		$form->addTextArea('description_short', 'Krátký popis', 80, 5)
			->addConditionOn($form['description_long'], $form::MaxLength, 100)
			->setRequired('Nezadal jsi dlouhý popis, zadej krátký');

		$form->addFloat('price', 'Cena')
			->setRequired('Zadejte cenu')
			->addRule($form::Min, 'Cena musí být alespoň %d ', 1);

		/** @TODO image upload */

		$form->addSubmit('submit', 'Odeslat');
		$form->onSuccess[] = [$this, 'formSubmitted'];
		return $form;
	}

	public function formSubmitted(Form $form, $data)
	{
		bdump($form->getHttpData()); // Very RAW style
	}

}