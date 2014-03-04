<?php

/*
 * Copyright (c) 2013, Flaming Code
 * 
 */

namespace FlamingUser\Form;

use FlamingUser\Entity\UserInterface;

use Zend\Form\Form;

/**
 * UserForm
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2013, Flaming Code
 * @link http://github.com/FlamingCode/FlamingUser for the canonical source repository
 * @license http://opensource.org/licenses/MIT MIT
 */
class UserForm extends Form
{
	/**
	 *
	 * @param string $name
	 * @param array $options
	 */
	public function __construct($name = null, $options = array())
	{
		parent::__construct('user', $options);
		$this->setAttribute('method', 'post');
		$this->setAttribute('class', 'form-horizontal');

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
				'placeholder' => 'Password',
				'autocomplete' => 'off',
				'class' => 'span3',
			),
			'options' => array(
				'label' => 'Password',
				'label_attributes' => array(
					'class' => 'required'
				),
				'bootstrap' => array(
					'help' => array(
						'style' => 'block',
						'content' => 'Minimum 6 characters'
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
				'label_attributes' => array(
					'class' => 'required'
				),
			),
		));

		$this->add(array(
			'name' => 'role',
			'type' => 'Zend\Form\Element\Radio',
			'attributes' => array(
				'value' => UserInterface::ROLE_USER,
			),
			'options' => array(
				'label' => 'Role',
				'value_options' => array(
					UserInterface::ROLE_USER => UserInterface::ROLE_USER
				),
			),
		));

		$this->add(array(
			'name' => 'active',
			'type' => 'Zend\Form\Element\Checkbox',
			'options' => array(
				'label' => 'Active',
			),
			'attributes' => array(
				'id' => 'active'
			)
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

	public function setAdminMode($flag = true)
	{
		$roleElement = $this->get('role');
		if ($flag) {
			$roleElement->setValueOptions(array(
				UserInterface::ROLE_USER => UserInterface::ROLE_USER,
				UserInterface::ROLE_ADMIN => UserInterface::ROLE_ADMIN,
			));

		} else {
			$roleElement->setValueOptions(array(
				UserInterface::ROLE_USER => UserInterface::ROLE_USER,
			));
		}
	}
}