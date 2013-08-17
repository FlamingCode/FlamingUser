<?php

/*
 * Copyright (c) 2013, Flaming Code
 * 
 */

namespace FlamingUser\Cli;

use FlamingBase\Controller\AbstractCliController;

use FlamingUser\Service\UserService;

use FlamingBase\Stdlib\StringTool;

use Zend\Console\Request as ConsoleRequest;

/**
 * UserController
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2013, Flaming Code
 */
class UserController extends AbstractCliController
{
	/**
	 *
	 * @var UserService
	 */
	protected $userService;

	public function addAction()
	{
		$request = $this->getRequest();
		if (!$request instanceof ConsoleRequest)
			throw new \RuntimeException('You can only use this action from a console!');

		$user = $this->getUserService()->createUser(array(
			'email' => $request->getParam('email'),
			'password' => $request->getParam('password'),
			'role' => $request->getParam('role', 'user'),
			'firstname' => $request->getParam('firstname', ''),
			'surname' => $request->getParam('surname', ''),
			'active' => true
		));

		$output = "User created\n";
		if (!$user->getId())
			return "Error saving user!\n";

		if (($request->getParam('generate-password') || $request->getParam('g')) && !$request->getParam('password')) {
			$password = StringTool::randStr(8);
			$output .= "With password: $password\n";
			$this->getUserService()->updateUser($user, array('password' => $password));
		}

		return $output;
	}

	/**
	 *
	 * @return UserService
	 */
	public function getUserService()
	{
		if (!$this->userService)
			$this->userService = $this->getServiceLocator()->get('FlamingUser\Service\UserService');
		return $this->userService;
	}
}