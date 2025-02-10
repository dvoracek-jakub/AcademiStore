<?php

declare(strict_types=1);

namespace App\Model\Order;



class NewOrderFacade
{

	/**
	 * @TODO udělej to tak, aby nová objednávka šla vytvořit i třeba z admina
	 *       Tedy ať všechny potřebné parametry přebírá a je mu jedno, kdo mu je zaslal
	 *        - jestli to byl formulář na frontendu, nebo třeba API
	 */

	public function processOrder() {
		die("<br>\n".time().' ['.__LINE__."] shlaka");
	}

}