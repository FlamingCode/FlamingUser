<?php

/*
 * Copyright (c) 2013, Flaming Code
 * 
 */

namespace FlamingUser\InputFilter;

use FlamingBase\InputFilter\Factory as InputFactory;

use Zend\InputFilter\InputFilter;

/**
 * ChangePasswordFilter
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2013, Flaming Code
 * @link http://github.com/FlamingCode/FlamingUser for the canonical source repository
 * @license http://opensource.org/licenses/MIT MIT
 */
class ChangePasswordFilter extends InputFilter
{
	public function __construct()
	{
		$this->setFactory(new InputFactory);
		
		$this->add(array(
			'name' => 'password',
			'type' => 'FlamingBase\InputFilter\PostValidationFilterableInput',
			'required' => true,
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
			'post_validation_filters' => array(
				array('name' => 'FlamingBase\Filter\Bcrypt')
			)
		));

		$this->add(array(
			'name' => 'passwordVerify',
			'required' => true,
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