<?php

/*
 * Copyright (c) 2013, Flaming Code
 * 
 */

namespace FlamingUser\Form;

use Zend\Form\Form;

/**
 * ChangePasswordForm
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2013, Flaming Code
 * @link http://github.com/FlamingCode/FlamingUser for the canonical source repository
 * @license http://opensource.org/licenses/MIT MIT
 */
class ChangePasswordForm extends Form
{
	public function __construct($name = null, $options = array())
	{
		parent::__construct('changePassword', $options);
		$this->setAttribute('method', 'post');
		$this->setAttribute('class', 'form-horizontal');

		$this->add(array(
			'name' => 'password',
			'type' => 'Zend\Form\Element\Password',
			'attributes' => array(
				'placeholder' => 'Password',
				'autocomplete' => 'off'
			),
			'options' => array(
				'label' => 'Password',
				'label_attributes' => array(
					'class' => 'required'
				),
				'bootstrap' => array(
					'help' => array(
						'style' => 'block',
						'content' => 'Minimum 6 characters',
					)
				)
			),
		));

		$this->add(array(
			'name' => 'passwordVerify',
			'type' => 'Zend\Form\Element\Password',
			'attributes' => array(
				'placeholder' => 'Repeat password',
				'autocomplete' => 'off'
			),
			'options' => array(
				'label' => 'Repeat password',
				'label_attributes' => array(
					'class' => 'required'
				),
			),
		));

		// Csrf
		$this->add(array(
			'type' => 'Zend\Form\Element\Csrf',
			'name' => 'csrf',
			'options' => array(
				'csrf_options' => array(
					'timeout' => 600
				)
			)
		));

		$this->add(array(
			'name' => 'submit',
			'type' => 'Zend\Form\Element\Submit',
			'attributes' => array(
				'value' => 'Change password',
				'class' => 'btn btn-success'
			),
			'options' => array(

			),
		));
	}
}