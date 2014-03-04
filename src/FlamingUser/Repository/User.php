<?php

/*
 * Copyright (c) 2013, Flaming Code
 * 
 */

namespace FlamingUser\Repository;

use Doctrine\ORM\EntityRepository;

use FlamingUser\Entity\UserInterface;

/**
 * User
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2013, Flaming Code
 * @link http://github.com/FlamingCode/FlamingUser for the canonical source repository
 * @license http://opensource.org/licenses/MIT MIT
 */
class User extends EntityRepository
{
	protected $defaultOrderby = array(
		'firstname' => 'ASC',
		'surname' => 'ASC'
	);

	protected $defaultCriteria = array();

	public function findAll($includeAdmins = false, $limit = null, $offset = null)
	{
		$criteria = array();

		if (!$includeAdmins) {
			$criteria['role'] = array(
				UserInterface::ROLE_USER,
				UserInterface::ROLE_MANAGER
			);
		}

		$criteria = $this->getCriteria($criteria);

		return $this->findBy($criteria, $this->getOrderBy(), $limit, $offset);
	}

	public function getOrderBy(array $orderBy = null)
	{
		$orderBy = $orderBy ?: array();
		return array_merge($this->defaultOrderby, $orderBy);
	}

	public function getCriteria(array $criteria = array())
	{
		return array_merge($criteria, $this->defaultCriteria);
	}
}