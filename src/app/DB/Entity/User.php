<?php declare(strict_types = 1);

namespace App\DB\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\DB\Repository\UserRepository")
 * @ORM\Table(name="`user`")
 */
class User extends AbstractEntity
{

	/** @ORM\Column(type="string") */
	private string $username;

	/** @ORM\Column(type="datetime") */
	private DateTime $createdAt;

	/** @ORM\Column(type="datetime", nullable=true) */
	private ?DateTime $updatedAt = null;

	public function __construct(string $username)
	{
		$this->username = $username;
		$this->createdAt = new DateTime();
	}

	public function getUsername(): string
	{
		return $this->username;
	}
/*
	public function setUsername(string $username): void
	{
		$this->username = $username;
	}

	public function getCreatedAt(): DateTime
	{
		return $this->createdAt;
	}

	public function setCreatedAt(DateTime $createdAt): void
	{
		$this->createdAt = $createdAt;
	}

	public function getUpdatedAt(): ?DateTime
	{
		return $this->updatedAt;
	}

	public function setUpdatedAt(?DateTime $updatedAt): void
	{
		$this->updatedAt = $updatedAt;
	}*/

}
