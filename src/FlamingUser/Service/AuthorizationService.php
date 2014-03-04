<?php

/*
 * Copyright (c) 2013, Flaming Code
 * 
 */

namespace FlamingUser\Service;

use Zend\Permissions\Acl\AclInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\MvcEvent;

/**
 * AuthorizationService
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2013, Flaming Code
 * @link http://github.com/FlamingCode/FlamingUser for the canonical source repository
 * @license http://opensource.org/licenses/MIT MIT
 */
class AuthorizationService
{
	/**
	 *
	 * @var AclInterface
	 */
	protected $acl;

	/**
	 *
	 * @var AuthenticationService
	 */
	protected $authService;

	/**
	 *
	 * @var string
	 */
	protected $redirectRoute = 'home';
	
	protected $loginRoute = 'flaminguser/login';

	public function getRole()
	{
		if ($this->getAuthService()->hasIdentity())
			return $this->getAuthService()->getIdentity()->getRole();

		return 'guest';
	}

	/**
	 *
	 * @return AuthenticationService
	 */
	public function getAuthService()
	{
		return $this->authService;
	}

	/**
	 *
	 * @param AuthenticationService $authService
	 * @return AuthorizationService
	 */
	public function setAuthService(AuthenticationService $authService)
	{
		$this->authService = $authService;
		return $this;
	}

	/**
	 *
	 * @return AclInterface
	 */
	public function getAcl()
	{
		return $this->acl;
	}

	/**
	 *
	 * @param AclInterface $acl
	 * @return AuthorizationService
	 */
	public function setAcl(AclInterface $acl)
	{
		$this->acl = $acl;
		return $this;
	}

	public function onPreDispatch(MvcEvent $event)
	{
		$routeMatch = $event->getRouteMatch();
		$route = $routeMatch->getMatchedRouteName();
		$actionName = $routeMatch->getParam('action', 'index');

		if (!$this->getAcl()->hasResource($route)) {
			throw new \Exception(sprintf('The requested resource is not in the ACL! Resource: %s, Priviledge: %s',
			                             $route, $actionName));
		}

		if (!$this->getAcl()->isAllowed($this->getRole(), $route, $actionName)) {
			$event->stopPropagation();

			$url = $event->getRouter()->assemble(array(), array('name' => $this->getRedirectRoute()));
			if ('guest' === $this->getRole()) {
				$query = array(
					'r' => $event->getRouter()->assemble(array(
						'action' => $actionName
					), array(
						'name' => $route
					))
				);
				$url = $event->getRouter()->assemble(
					array(), 
					array(
						'name' => $this->getLoginRoute(),
						'query' => $query
					)
				);
			}
			
			$response = $event->getResponse();
			$response->getHeaders()->addHeaderLine('Location', $url);
			$response->setStatusCode(302);
			$response->sendHeaders();
		}
	}

	public function getRedirectRoute()
	{
		return $this->redirectRoute;
	}

	public function setRedirectRoute($route)
	{
		$this->redirectRoute = (string) $route;
		return $this;
	}
	
	public function getLoginRoute()
	{
		return $this->loginRoute;
	}

	public function setLoginRoute($route)
	{
		$this->loginRoute = (string) $route;
		return $this;
	}
}