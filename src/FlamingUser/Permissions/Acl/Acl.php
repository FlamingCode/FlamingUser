<?php

/*
 * Copyright (c) 2013, Flaming Code
 * 
 */

namespace FlamingUser\Permissions\Acl;

use Zend\Permissions\Acl\Acl as ZendAcl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;

/**
 * Acl
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2013, Flaming Code
 * @link http://github.com/FlamingCode/FlamingUser for the canonical source repository
 * @license http://opensource.org/licenses/MIT MIT
 */
class Acl extends ZendAcl
{
	const DEFAULT_ROLE = 'guest';

	/**
	 *
	 * @param array $config
	 * @throws Exception\InvalidArgumentException
	 */
	public function __construct(array $config)
	{
		if (!isset($config['roles']) || !isset($config['resources']))
			throw new Exception\InvalidArgumentException('Invalid ACL Config found');

		$roles = $config['roles'];

		if (!isset($roles[self::DEFAULT_ROLE]))
			$roles[self::DEFAULT_ROLE] = null;

		$this->addRoles($roles)
		     ->addResources($config['resources']);
	}

	protected function addRoles(array $roles)
	{
		foreach ($roles as $name => $parent) {
			if (!$this->hasRole($name)) {
				if (empty($parent))
					$parent = array();
				else
					$parent = explode(',', $parent);

				$this->addRole(new Role($name), $parent);
			}
		}

		return $this;
	}

	protected function addResources($resources)
	{
		foreach ($resources as $permission => $routes) {
			foreach ($routes as $route => $actions) {
				if ($route == 'all' || $route == '*')
					$route = null;
				else {
					if (!$this->hasResource($route))
						$this->addResource(new Resource($route));
				}

				foreach ($actions as $action => $role) {
					if ($action == 'all' || $action == '*')
						$action = null;

					if (!is_array($role))
						$role = array($role);

					if ($permission == 'allow') {
						foreach ($role as $allowedRole)
							$this->allow($allowedRole, $route, $action);
					} elseif ($permission == 'deny') {
						foreach ($role as $deniedRole)
							$this->deny($deniedRole, $route, $action);
					} else
						throw new Exception\InvalidArgumentException('No valid permission defined: ' . $permission);
				}
			}
		}

		return $this;
	}
}