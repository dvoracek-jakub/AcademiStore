<?php

declare(strict_types=1);

namespace App\Core;

use Nette;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;

		// FRONTEND
		$router->addRoute('/', 'Front:Home:default');
		$router->addRoute('/doctrine', 'Front:Home:doctrine');

		// ADMIN
		$router->addRoute('/admin', 'Admin:Dashboard:default');

		$router->addRoute('<presenter>/<action>[/<id>]', 'Home:default');
		return $router;
	}
}
