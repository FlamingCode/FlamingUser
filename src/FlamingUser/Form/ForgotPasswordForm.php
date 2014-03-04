<?php

/*
 * Copyright (c) 2013, Flaming Code
 * 
 */

namespace FlamingUser\Form;

use Zend\Form\Form;

/**
 * ForgotPasswordForm
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2013, Flaming Code
 * @link http://github.com/FlamingCode/FlamingUser for the canonical source repository
 * @license http://opensource.org/licenses/MIT MIT
 */
class ForgotPasswordForm extends Form
{
	public function __construct($name = null, $options = array())
	{
		parent::__construct('forgotPassword', $options);
		$this->setAttribute('method', 'post');
		$this->setAttribute('class', 'form-horizontal');

		$this->add(array(
			'name' => 'email',
			'type' => 'Zend\Form\Element\Email',
			'attributes' => array(
				'placeholder' => 'Email',
			),
			'options' => array(
				'label' => 'Email',
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
				'value' => 'Send email',
				'class' => 'btn btn-success'
			),
			'options' => array(

			),
		));
	}
}