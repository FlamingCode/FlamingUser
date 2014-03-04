<?php

/*
 * Copyright (c) 2013, Flaming Code
 * 
 */

namespace FlamingUser\Entity;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

use FlamingBase\Entity\AbstractEntity;

/**
 * User
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2013, Flaming Code
 * @link http://github.com/FlamingCode/FlamingUser for the canonical source repository
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @ORM\Entity(repositoryClass="FlamingUser\Repository\User")
 * @ORM\Table(name="users")
 */
class User extends AbstractEntity implements UserInterface
{
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 * @var int
	 **/
	protected $id;

	/**
	 * @ORM\Column(type="string", unique=TRUE)
	 * @var string
	 **/
	protected $email = '';

	/**
	 * @ORM\Column(type="string", length=60)
	 * @var string
	 **/
	protected $password;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 **/
	protected $role = self::ROLE_USER;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 **/
	protected $firstname = '';

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 **/
	protected $surname = '';

	/**
	 * @ORM\Column(type="boolean")
	 * @var bool
	 **/
	protected $emailConfirmed = true;

	/**
	 * @ORM\Column(type="boolean")
	 * @var bool
	 **/
	protected $active = false;

	/**
	 * @ORM\Column(type="string", length=64, unique=TRUE, nullable=TRUE)
	 * @var string
	 **/
	protected $forgotPassHash = null;

	public function getId()
	{
		return $this->id;
	}

	public function getEmail()
	{
		return $this->email;
	}

	public function setEmail($email)
	{
		$this->email = (string) $email;
		return $this;
	}

	public function getPassword()
	{
		return $this->password;
	}

	public function setPassword($password)
	{
		if (!empty($password))
			$this->password = (string) $password;
		return $this;
	}

	public function getRole()
	{
		return $this->role;
	}

	public function setRole($role)
	{
		$this->role = $role;
		return $this;
	}

	public function getFirstname()
	{
		return $this->firstname;
	}

	public function setFirstname($firstname)
	{
		$this->firstname = (string) $firstname;
		return $this;
	}

	public function getSurname()
	{
		return $this->surname;
	}

	public function setSurname($surname)
	{
		$this->surname = (string) $surname;
		return $this;
	}

	public function getFullName()
	{
		return $this->firstname . ' ' . $this->surname;
	}

	public function getEmailConfirmed()
	{
		return $this->emailConfirmed;
	}

	public function setEmailConfirmed($flag = true)
	{
		$this->emailConfirmed = (bool) $flag;
		return $this;
	}

	public function getActive()
	{
		return $this->active;
	}

	public function setActive($flag = true)
	{
		$this->active = (bool) $flag;
		return $this;
	}

	public function getForgotPassHash()
	{
		return $this->forgotPassHash;
	}

	public function setForgotPassHash($hash = null)
	{
		$this->forgotPassHash = $hash;
		return $this;
	}
}