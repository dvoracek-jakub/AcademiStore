<?php declare(strict_types=1);

namespace App\Core;

class Settings
{

	public $store;

	public function __construct(array $store, public bool $debugMode, public string $appDir)
	{
		$this->store = (object) $store;
	}

}