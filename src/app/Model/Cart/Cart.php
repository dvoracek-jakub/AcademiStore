<?php

declare(strict_types=1);

namespace App\Model\Cart;

use App\Model\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use App\Model\Customer\Customer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Model\Cart\CartRepository")
 * @ORM\Table(name="`cart`")
 */
class Cart extends AbstractEntity
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected int $id;

	/**
	 * @ORM\ManyToOne(targetEntity="\App\Model\Customer\Customer", inversedBy="carts")
	 * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", nullable=true)
	 */
	private Customer $customer;

	public function getCustomer(): Customer
	{
		return $this->product;
	}

	public function setCustomer(?Customer $customer)
	{
		$this->customer = $customer;
	}

}