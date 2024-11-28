<?php declare(strict_types=1);

namespace App\Module\Admin\Accessory\Form;

use App\Model\Product\ProductFacade;
use \Nette\Application\UI\Form;

class ProductFormFactory
{

	public function __construct(public ProductFacade $productFacade, private \App\Core\Settings $settings) {

	}

	public function createProductForm(): Form
	{
		$form = new Form();
		$form->addText('name', 'Název')
			->setRequired('Zadejte název');

		$form->addText('sku', 'SKU');

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
		$max_imgage_size =  $this->settings->store->product_image_max_size_kb;
		$form->addUpload('image', 'Obrázek')
			->addRule($form::Image, 'Avatar musí být JPEG, PNG, GIF, WebP nebo AVIF')
			->addRule($form::MaxFileSize, "Maximální velikost je $max_imgage_size kB.", $max_imgage_size * 1024);

		$form->onSuccess[] = [$this, 'formSubmitted'];
		$form->addSubmit('submit', 'Odeslat');
		return $form;
	}

	public function formSubmitted(Form $form, $data)
	{
		if ($this->productFacade->skuExists($data->sku)) {
			$form->addError('Zadané SKU již existuje');
		}

		$data->imageName = $this->processImage($form, $data->image);

		if (!$form->hasErrors()) {
			$this->productFacade->createProduct($data);
		}
	}

	private function processImage(Form $form, \Nette\Http\FileUpload $image)
	{
		$settings = $this->settings->store;
		if($image->isImage() and $image->isOk()) {

			if ($image->getSize() > $settings->product_image_max_size_kb * 1024) {
				$form->addError('Nahraný obrázek je příliš velký.');
			}

			$uploadDir = $_SERVER['DOCUMENT_ROOT'] . $settings->product_image_path;
			$fileName = bin2hex(random_bytes(12)) . '.' . pathinfo($image->getSanitizedName(), PATHINFO_EXTENSION);
			$image->move($uploadDir . $fileName);

			// Generate thumbnail medium
			$imageResource = \Nette\Utils\Image::fromFile($uploadDir . $fileName);
			$imageResource->resize(500, 500, \Nette\Utils\Image::EXACT);
			$imageResource->save($uploadDir . pathinfo($fileName, PATHINFO_FILENAME) . '_500x500.' . pathinfo($fileName, PATHINFO_EXTENSION));

			// Generate thumbnail small
			$imageResource = \Nette\Utils\Image::fromFile($uploadDir . $fileName);
			$imageResource->resize(200, 200, \Nette\Utils\Image::EXACT);
			$imageResource->save($uploadDir . pathinfo($fileName, PATHINFO_FILENAME) . '_200x200.' . pathinfo($fileName, PATHINFO_EXTENSION));

			return $fileName;
		}
		return '';
	}



}