<?php

declare(strict_types=1);

namespace App\Model\Payment;

use App\Model\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Model\Payment\PaymentRepository")
 * @ORM\Table(name="`payment`")
 */
class Payment extends AbstractEntity
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected int $id;

	/**
	 * @ORM\Column(type="string")
	 *
	 */
	private $name;

	/**
	 * @ORM\Column(type="decimal")
	 */
	private $price;

	/**
	 * @ORM\Column(type="integer")
	 */
	private $priority;

	/**
	 * @return mixed
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param  mixed  $name
	 */
	public function setName($name): void
	{
		$this->name = $name;
	}

	/**
	 * @return mixed
	 */
	public function getPrice()
	{
		return $this->price;
	}

	/**
	 * @param  mixed  $price
	 */
	public function setPrice($price): void
	{
		$this->price = $price;
	}

	/**
	 * @return mixed
	 */
	public function getPriority()
	{
		return $this->priority;
	}

	/**
	 * @param  mixed  $priority
	 */
	public function setPriority($priority): void
	{
		$this->priority = $priority;
	}

}