<?php

/*
 * Copyright (c) 2013, Flaming Code
 * 
 */

namespace FlamingUser\Filter;

use Zend\InputFilter\InputFilter;
use Doctrine\ORM\EntityManager;

/**
 * ProfileFilter
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2013, Flaming Code
 */
class ProfileFilter extends InputFilter
{
	public function __construct(EntityManager $em)
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
						'object_repository' => $em->getRepository('Tasti\Entity\User'),
						'fields' => array('email'),
						'messages' => array(
							'objectNotUnique' => 'Der findes allerede en bruger i systemet med denne emailadresse'
						)
					)
				)
			),
		));

		$this->add(array(
			'name' => 'password',
			'required' => false,
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
			),
		));

		$this->add(array(
			'name' => 'passwordVerify',
			'required' => false,
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