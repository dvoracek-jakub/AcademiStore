<?php declare(strict_types=1);

namespace App\Model\User;

use App\Model\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Model\User\UserRepository")
 * @ORM\Table(name="`user`")
 */
class User extends AbstractEntity
{

	/** @ORM\Column(type="string") */
	private string $username;

	/** @ORM\Column(type="string") */
	private string $password;

	/** @ORM\Column(type="string") */
	private string $firstname = '';

	/** @ORM\Column(type="string") */
	private string $lastname = '';

	/** @ORM\Column(type="datetime") */
	//private DateTime $createdAt;

	/** @ORM\Column(type="datetime", nullable=true) */
	//private ?DateTime $updatedAt = null;

	/*public function __construct(string $username)
	{
		$this->username = $username;
		//$this->createdAt = new DateTime();
	}*/

	public function getUsername(): string
	{
		return $this->username;
	}

	public function setUsername(string $username): void
	{
		$this->username = $username;
	}

	public function setPassword(string $password): void
	{
		$this->password = $password;
	}

	public function getFirstname(): string
	{
		return $this->firstname;
	}

	public function setFirstname(string $firstname): void
	{
		$this->firstname = $firstname;
	}

	public function getLastname(): string
	{
		return $this->lastname;
	}

	public function setLastname(string $lastname): void
	{
		$this->lastname = $lastname;
	}

}
