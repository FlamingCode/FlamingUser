<?php

/*
 * Copyright (c) 2013, Flaming Code
 * 
 */

namespace FlamingUser\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Storage\StorageInterface;

/**
 * Authentication
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2013, Flaming Code
 * @link http://github.com/FlamingCode/FlamingUser for the canonical source repository
 * @license http://opensource.org/licenses/MIT MIT
 */
class Authentication extends AbstractPlugin
{
	/**
	 * @var AuthenticationService
	 */
	protected $authService;

	/**
	 * Proxy convenience method
	 *
	 * @return bool
	 */
	public function hasIdentity()
	{
		return $this->getService()->hasIdentity();
	}

	/**
	 * Proxy convenience method
	 *
	 * @return mixed
	 */
	public function getIdentity()
	{
		return $this->getService()->getIdentity();
	}

	/**
	 * Proxy convenience method
	 *
	 * @return void
	 */
	public function clearIdentity()
	{
		$this->getService()->clearIdentity();
	}

	/**
	 *
	 * @return AdapterInterface
	 */
	public function getAdapter()
	{
		return $this->getService()->getAdapter();
	}

	/**
	 *
	 * @return StorageInterface
	 */
	public function getStorage()
	{
		return $this->getService()->getStorage();
	}

	/**
	 * Get authService.
	 *
	 * @return AuthenticationService
	 */
	public function getService()
	{
		return $this->authService;
	}

	/**
	 * Set authService.
	 *
	 * @param AuthenticationService $authService
	 */
	public function setService(AuthenticationService $authService)
	{
		$this->authService = $authService;
		return $this;
	}
}