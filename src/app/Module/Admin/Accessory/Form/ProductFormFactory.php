<?php declare(strict_types=1);

namespace App\Module\Admin\Accessory\Form;

use App\Model\Product\ProductFacade;
use Nette\Application\UI\Form;
use App\Model\Product\ProductImage;
use App\Model\Product\Product;

class ProductFormFactory
{

	private string $action;
	private ?int $id;

	public function __construct(public ProductFacade $productFacade, private \App\Core\Settings $settings, private ProductImage $productImage) {}

	public function createProductForm(string $action, ?Product $product = null): Form
	{
		$this->action = $action;
		$this->id = $product ? $product->getId() : null;

		$form = new Form();
		$form->addText('name', 'Název')
			->setRequired('Zadejte název');

		$form->addText('sku', 'SKU');

		$form->addTextArea('descLong', 'Dlouhý popis', 80, 18)
			->setRequired('Zadejte dlouhý popis')
			->addRule($form::MinLength, 'Dlouhý popis musí mít alespoň %d znaků', 3); // 300

		$form->addTextArea('descShort', 'Krátký popis', 80, 5)
			->addConditionOn($form['descShort'], $form::MaxLength, 1) // 100
			->setRequired('Nezadal jsi dlouhý popis, zadej krátký');

		$form->addFloat('price', 'Cena')
			->setRequired('Zadejte cenu')
			->addRule($form::Min, 'Cena musí být alespoň %d ', 1);

		$max_imgage_size = $this->settings->store->product_image_max_size_kb;
		$form->addUpload('image', 'Obrázek')
			->addRule($form::Image, 'Avatar musí být JPEG, PNG, GIF, WebP nebo AVIF')
			->addRule($form::MaxFileSize, "Maximální velikost je $max_imgage_size kB.", $max_imgage_size * 1024);

		$form->addCheckbox('deleteImage', 'Smazat obrázek?');

		$form->addCheckbox('active', 'Aktivní');

		if ($this->action == 'edit') {
			//$form->setDefaults($product); ⛔
			$form['name']->setDefaultValue($product->getName());
			$form['sku']->setDefaultValue($product->getSku());
			$form['price']->setDefaultValue($product->getPrice());
			$form['descShort']->setDefaultValue($product->getDescShort());
			$form['descLong']->setDefaultValue($product->getDescLong());
			$form['active']->setDefaultValue($product->getActive());
		}

		$form->onSuccess[] = [$this, 'formSubmitted'];
		$form->addSubmit('submit', 'Odeslat');
		return $form;
	}

	public function formSubmitted(Form $form, $data)
	{
		if ($this->action == 'create' && $this->productFacade->skuExists($data->sku)) {
			$form->addError('Zadané SKU již existuje');
		}

		if ($this->action == 'edit') {
			// Replacing existing image OR deleting the old one
			if ($data->image->isImage() && $data->image->isOk() || $data->deleteImage) {
				$this->productFacade->deleteImage($this->id);
			}
		}

		$data->imageName = $this->processImage($form, $data->image);

		if (!$form->hasErrors()) {
			$this->productFacade->saveProduct($data, $this->id);
		}
	}

	private function processImage(Form $form, \Nette\Http\FileUpload $image)
	{
		$settings = $this->settings->store;
		if ($image->isImage() && $image->isOk()) {

			if ($image->getSize() > $settings->product_image_max_size_kb * 1024) {
				$form->addError('Nahraný obrázek je příliš velký.');
			}

			$uploadDir = $_SERVER['DOCUMENT_ROOT'] . $settings->product_image_path;
			$fileName = bin2hex(random_bytes(12)) . '.' . pathinfo($image->getSanitizedName(), PATHINFO_EXTENSION);
			$image->move($uploadDir . $fileName);

			// Generate thumbnails
			$this->productImage->generateThumbnail($uploadDir, $fileName, $settings->product_image_medium);
			$this->productImage->generateThumbnail($uploadDir, $fileName, $settings->product_image_small);

			return $fileName;
		}
		return '';
	}

}