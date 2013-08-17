<?php

/*
 * Copyright (c) 2013, Flaming Code
 * 
 */

namespace FlamingUser\Cli;

use FlamingBase\Controller\AbstractCliController;

use FlamingUser\Service\UserService;

use FlamingBase\Stdlib\StringTool;
use FlamingUser\InputFilter\UserFilter;

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
	
	/**
	 *
	 * @var UserFilter
	 */
	protected $userFilter;

	public function addAction()
	{
		$request = $this->getRequest();
		if (!$request instanceof ConsoleRequest)
			throw new \RuntimeException('You can only use this action from a console!');
		
		$password = $request->getParam('password');
		if (($request->getParam('generate-password') || $request->getParam('g')) && !$request->getParam('password')) {
			$password = StringTool::randStr(8);
			$output .= "With password: $password\n";
		}
		
		$user = array(
			'email' => $request->getParam('email'),
			'password' => $password,
			'role' => $request->getParam('role', 'user'),
			'firstname' => $request->getParam('firstname', ''),
			'surname' => $request->getParam('surname', ''),
			'active' => true
		);
		
		$filter = $this->getUserFilter();
		$filter->get('id')->setRequired(false);
		$filter->get('passwordVerify')->setRequired(false);
		$filter->setData($user);
		if (!$filter->isValid()) {
			$errors = array();
			foreach ($filter->getMessages() as $input => $msg) {
				$error = $input . ': ' .PHP_EOL;
				foreach ($msg as $m)
					$error .= $m . PHP_EOL;
				$errors[] = $error;
			}
			return implode(PHP_EOL, $errors);
		}

		$user = $this->getUserService()->createUser($filter->getValues());

		if (!$user->getId())
			return "Error saving user!\n";

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
	
	/**
	 * 
	 * @return UserFilter
	 */
	public function getUserFilter()
	{
		if (!$this->userFilter)
			$this->userFilter = $this->getServiceLocator()->get('FlamingUser\InputFilter\UserFilter');
		return $this->userFilter;
	}
}