<?php

/*
 * Copyright (c) 2013, Flaming Code
 * 
 */

namespace FlamingUser\Authentication\Storage;

use Zend\Authentication\Storage\Session as ZendSessionStorage;
use Zend\Session\ManagerInterface as SessionManagerInterface;

use Doctrine\ORM\EntityManager;

/**
 * DoctrineEntity
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2013, Flaming Code
 * @link http://github.com/FlamingCode/FlamingUser for the canonical source repository
 * @license http://opensource.org/licenses/MIT MIT
 */
class DoctrineEntity extends ZendSessionStorage
{
	/**
	 *
	 * @var EntityManager
	 */
	protected $entityManager;

	public function __construct(EntityManager $entityManager = null, $namespace = null, $member = null, SessionManagerInterface $manager = null)
	{
		if (null !== $entityManager)
			$this->setEntityManager($entityManager);
		parent::__construct($namespace, $member, $manager);
	}

	public function isEmpty()
	{
		return parent::isEmpty() || $this->read() === null;
	}

	public function read()
	{
		$data = parent::read();
		if (!is_array($data) || empty($data) || !array_key_exists('id', $data) ||
		    !array_key_exists('class', $data))
			return null;
		$data = $this->getEntityManager()->find($data['class'], $data['id']);
		if (!$data)
			parent::write(null);
		return $data;
	}

	public function write($contents)
	{
		$className = get_class($contents);
		$id = $this->getEntityManager()
		           ->getClassMetadata($className)
		           ->getIdentifierValues($contents);
		if (empty($id))
			$contents = null;
		else {
			$contents = array(
				'class' => $className,
				'id' => $id
			);
		}
		parent::write($contents);
	}

	/**
	 *
	 * @return EntityManager
	 */
	public function getEntityManager()
	{
		return $this->entityManager;
	}

	/**
	 *
	 * @param EntityManager $entityManager
	 * @return DoctrineEntity
	 */
	public function setEntityManager(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
		return $this;
	}
}