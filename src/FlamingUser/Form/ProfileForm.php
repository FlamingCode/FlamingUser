<?php

/*
 * Copyright (c) 2013, Flaming Code
 * 
 */

namespace FlamingUser\Form;

use Zend\Form\Form;

/**
 * ProfileForm
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2013, Flaming Code
 * @link http://github.com/FlamingCode/FlamingUser for the canonical source repository
 * @license http://opensource.org/licenses/MIT MIT
 */
class ProfileForm extends Form
{
	public function __construct($name = null, $options = array())
	{
		parent::__construct('profile', $options);
		$this->setAttribute('method', 'post');
		$this->setAttribute('class', 'form-horizontal footer-actions');

		$this->add(array(
			'name' => 'id',
			'type' => 'Zend\Form\Element\Hidden',
			'attributes' => array(

			),
		));

		$this->add(array(
			'name' => 'firstname',
			'type' => 'Zend\Form\Element\Text',
			'attributes' => array(
				'placeholder' => 'Firstname',
				'class' => 'span3',
			),
			'options' => array(
				'label' => 'Firstname',
				'label_attributes' => array(
					'class' => 'required'
				),
			),
		));

		$this->add(array(
			'name' => 'surname',
			'type' => 'Zend\Form\Element\Text',
			'attributes' => array(
				'placeholder' => 'Surname',
				'class' => 'span3',
			),
			'options' => array(
				'label' => 'Surname',
				'label_attributes' => array(
					'class' => 'required'
				),
			),
		));

		$this->add(array(
			'name' => 'email',
			'type' => 'Zend\Form\Element\Email',
			'attributes' => array(
				'placeholder' => 'Email',
				'class' => 'span3',
			),
			'options' => array(
				'label' => 'Email',
				'label_attributes' => array(
					'class' => 'required'
				),
			),
		));

		$this->add(array(
			'name' => 'password',
			'type' => 'Zend\Form\Element\Password',
			'attributes' => array(
				'placeholder' => 'New password',
				'autocomplete' => 'off',
				'class' => 'span3',
			),
			'options' => array(
				'label' => 'New password',
				'bootstrap' => array(
					'help' => array(
						'style' => 'block',
						'content' => 'Fill out to choose a new password'
					)
				),
			),
		));

		$this->add(array(
			'name' => 'passwordVerify',
			'type' => 'Zend\Form\Element\Password',
			'attributes' => array(
				'placeholder' => 'Repeat password',
				'autocomplete' => 'off',
				'class' => 'span3',
			),
			'options' => array(
				'label' => 'Repeat password',
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
				'value' => 'Save',
				'class' => 'btn btn-success'
			),
			'options' => array(

			),
		));
	}
}