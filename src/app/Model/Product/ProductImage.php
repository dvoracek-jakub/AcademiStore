<?php

namespace App\Model\Product;

class ProductImage
{

	public function __construct(private \App\Core\Settings $settings) {}

	public function generateThumbnail(string $folder, string $filename, string $dimensions)
	{
		$dim = explode('x', $dimensions);
		$imageResource = \Nette\Utils\Image::fromFile($folder . $filename);
		$imageResource->resize($dim[0], $dim[1], \Nette\Utils\Image::EXACT);
		$imageResource->save($folder . pathinfo($filename, PATHINFO_FILENAME) . '_' . $dimensions . '.' . pathinfo($filename, PATHINFO_EXTENSION));
	}

	public function getImage(string $filename, string $dimensions = '')
	{
		$folder = $this->settings->store->product_image_path;
		if ($dimensions !== '') {
			return $folder . pathinfo($filename, PATHINFO_FILENAME) . '_' . $dimensions . '.' . pathinfo($filename, PATHINFO_EXTENSION);
		} else {
			return $folder . $filename;
		}
	}

	public function deleteImage(string $filename)
	{
		$folder = $_SERVER['DOCUMENT_ROOT'] . $this->settings->store->product_image_path;
		$rawFilename = pathinfo($filename, PATHINFO_FILENAME);
		$extension = pathinfo($filename, PATHINFO_EXTENSION);

		/*echo($folder . $filename) . '<br>';
		echo($folder . $rawFilename . '_' . $this->settings->store->product_image_medium . '.' . $extension). '<br>';
		echo($folder . $rawFilename . '_' . $this->settings->store->product_image_small . '.' . $extension). '<br>';
		exit;*/
		unlink($folder . $filename);
		unlink($folder . $rawFilename . '_' . $this->settings->store->product_image_medium . '.' . $extension);
		unlink($folder . $rawFilename . '_' . $this->settings->store->product_image_small . '.' . $extension);
	}

}