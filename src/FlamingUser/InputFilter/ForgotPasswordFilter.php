<?php

/*
 * Copyright (c) 2013, Flaming Code
 * 
 */

namespace FlamingUser\InputFilter;

use Zend\InputFilter\InputFilter;

/**
 * ForgotPasswordFilter
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2013, Flaming Code
 * @link http://github.com/FlamingCode/FlamingUser for the canonical source repository
 * @license http://opensource.org/licenses/MIT MIT
 */
class ForgotPasswordFilter extends InputFilter
{
	public function __construct()
	{
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
			),
		));
	}
}