<?php

declare(strict_types=1);

namespace App\Model\Order;

use App\Model\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Model\Order\OrderRepository")
 * @ORM\Table(name="`order_payment`")
 */
class OrderPayment extends AbstractEntity
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", nullable=FALSE)
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected int $id;

	/**
	 * @ORM\OneToOne(targetEntity="App\Model\Order\Order", inversedBy="paymentStatus")
	 * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
	 */
	private $order;

	/**
	 * @ORM\Column(type="string", nullable=FALSE)
	 */
	private $status;

	/**
	 * @ORM\Column(type="string")
	 */
	private $remoteIdentifier;

	/**
	 * @ORM\Column(type="string")
	 */
	private $remoteState;

	/**
	 * @return mixed
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @param  mixed  $status
	 */
	public function setStatus($status): void
	{
		$this->status = $status;
	}

	/**
	 * @return mixed
	 */
	public function getOrder()
	{
		return $this->order;
	}

	/**
	 * @param  mixed  $order
	 */
	public function setOrder($order): void
	{
		$this->order = $order;
	}

	/**
	 * @return mixed
	 */
	public function getRemoteIdentifier()
	{
		return $this->remoteIdentifier;
	}

	/**
	 * @param  mixed  $remoteIdentifier
	 */
	public function setRemoteIdentifier($remoteIdentifier): void
	{
		$this->remoteIdentifier = $remoteIdentifier;
	}

	/**
	 * @return mixed
	 */
	public function getRemoteState()
	{
		return $this->remoteState;
	}

	/**
	 * @param  mixed  $remoteState
	 */
	public function setRemoteState($remoteState): void
	{
		$this->remoteState = $remoteState;
	}

}