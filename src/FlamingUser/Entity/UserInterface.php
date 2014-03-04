<?php

/*
 * Copyright (c) 2013, Flaming Code
 * 
 */

namespace FlamingUser\Entity;

/**
 * UserInterface
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2013, Flaming Code
 * @link http://github.com/FlamingCode/FlamingUser for the canonical source repository
 * @license http://opensource.org/licenses/MIT MIT
 */
interface UserInterface
{
	const ROLE_GUEST   = 'guest';
	const ROLE_USER	   = 'user';
	const ROLE_ADMIN   = 'admin';

	public function getId();

	public function getEmail();
	public function setEmail($email);

	public function getPassword();
	public function setPassword($password);

	public function getRole();
	public function setRole($role);

	public function getFirstname();
	public function setFirstname($firstname);

	public function getSurname();
	public function setSurname($surname);

	public function getFullName();

	public function getEmailConfirmed();
	public function setEmailConfirmed($flag = true);

	public function getActive();
	public function setActive($flag = true);

	public function getForgotPassHash();
	public function setForgotPassHash($hash);
}