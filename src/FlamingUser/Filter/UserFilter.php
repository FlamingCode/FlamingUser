<?php

/*
 * Copyright (c) 2013, Flaming Code
 * 
 */

namespace FlamingUser\Filter;

use Zend\InputFilter\InputFilter;
use Doctrine\ORM\EntityManager;

/**
 * UserFilter
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2013, Flaming Code
 */
class UserFilter extends InputFilter
{
	const FILTER_MODE_ADD = 'add';
	const FILTER_MODE_EDIT = 'edit';

	/**
	 *
	 * @param EntityManager $em
	 * @param string $mode Add or edit mode
	 */
	public function __construct(EntityManager $em, $mode = self::FILTER_MODE_ADD)
	{
		$this->add(array(
			'name' => 'id',
			'required' => true,
			'filters' => array(
				array('name' => 'Int'),
			),
		));

		$this->add(array(
			'name' => 'firstname',
			'required' => true,
			'filters' => array(
				array('name' => 'StripTags'),
				array('name' => 'StringTrim'),
			),
			'validators' => array(
				array(
					'name' => 'StringLength',
					'options' => array(
						'encoding' => 'UTF-8',
						'min' => 1,
						'max' => 100,
					),
				),
			),
		));

		$this->add(array(
			'name' => 'surname',
			'required' => true,
			'filters' => array(
				array('name' => 'StripTags'),
				array('name' => 'StringTrim'),
			),
			'validators' => array(
				array(
					'name' => 'StringLength',
					'options' => array(
						'encoding' => 'UTF-8',
						'min' => 1,
						'max' => 100,
					),
				),
			),
		));

		$this->add(array(
			'name' => 'phone',
			'required' => false,
			'filters' => array(
				array('name' => 'Digits'),
				array('name' => 'StringTrim'),
			),
			'validators' => array(
				array(
					'name' => 'StringLength',
					'options' => array(
						'encoding' => 'UTF-8',
						'min' => 8,
						'max' => 8,
					),
				),
				array(
					'name' => 'Digits',
					'options' => array(
						'encoding' => 'UTF-8',
					),
				),
			),
		));

		$this->add(array(
			'name' => 'email',
			'required' => true,
			'filters' => array(
				array('name' => 'StripTags'),
				array('name' => 'StringTrim'),
			),
			'validators' => array(
				array(
					'name' => 'EmailAddress',
				),

				array(
					'name' => 'DoctrineModule\Validator\UniqueObject',
					'options' => array(
						'object_manager' => $em,
						'object_repository' => $em->getRepository('FlamingUser\Entity\User'),
						'fields' => array('email'),
						'messages' => array(
							'objectNotUnique' => 'The email is already in use'
						)
					)
				)
			),
		));

		$passwordRequired = self::FILTER_MODE_ADD === $mode;

		$this->add(array(
			'name' => 'password',
			'required' => $passwordRequired,
			'filters' => array(

			),
			'validators' => array(
				array(
					'name' => 'StringLength',
					'options' => array(
						'encoding' => 'UTF-8',
						'min' => 6,
					),
				),
				array(
					'name' => 'identical',
					'options' => array(
						'token' => 'passwordVerify'
					)
				),
			),
		));

		$this->add(array(
			'name' => 'passwordVerify',
			'required' => $passwordRequired,
			'filters' => array(

			),
			'validators' => array(
				array(
					'name' => 'StringLength',
					'options' => array(
						'encoding' => 'UTF-8',
						'min' => 6,
					),
				),
				 array(
					'name' => 'identical',
					'options' => array(
						'token' => 'password'
					)
				),
			),
		));
	}
}