<?php

/*
 * Copyright (c) 2013, Flaming Code
 * 
 */

namespace FlamingUser\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\PhpEnvironment\Response;
use Zend\Form\FormInterface;

use FlamingUser\Entity\UserInterface;
use FlamingUser\Service\UserService;
use FlamingUser\InputFilter\UserFilter;

/**
 * UserController
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2013, Flaming Code
 * @link http://github.com/FlamingCode/FlamingUser for the canonical source repository
 * @license http://opensource.org/licenses/MIT MIT
 */
class UserController extends AbstractActionController
{
	/**
	 *
	 * @var UserService
	 */
	protected $userService;

	/**
	 *
	 * @var FormInterface
	 */
	protected $userForm;

	public function indexAction()
	{
		/* @var $loggedInUser UserInterface */
		$loggedInUser = $this->authentication()->getIdentity();
		$isAdmin = UserInterface::ROLE_ADMIN === $loggedInUser->getRole();

		return new ViewModel(array(
			'users' => $this->getUserService()->findUsers($isAdmin),
			'isAdmin' => $isAdmin
		));
	}

	public function addAction()
	{
		/* @var $loggedInUser UserInterface */
		$loggedInUser = $this->authentication()->getIdentity();
		$isAdmin = UserInterface::ROLE_ADMIN === $loggedInUser->getRole();

		$form = $this->getUserForm();
		$form->setAdminMode($isAdmin);

		$form->get('active')->setValue(true);

		$prg = $this->prg($this->url()->fromRoute('flaminguser/user', array(
			'action' => 'add'
		)), true);

		if ($prg instanceof Response) {
			return $prg;
		} elseif ($prg === false) {
			return array(
				'form' => $form,
				'isAdmin' => $isAdmin
			);
		}

		$form->setData($prg);
		if (!$form->isValid()) {
			return array(
				'form' => $form,
				'isAdmin' => $isAdmin
			);
		}

		$this->getUserService()->createUser($form->getData());
		$this->flashMessenger()->addSuccessMessage('User created');

		return $this->redirect()->toRoute('flaminguser/user');
	}

	public function editAction()
	{
		/* @var $loggedInUser UserInterface */
		$loggedInUser = $this->authentication()->getIdentity();
		$isAdmin = UserInterface::ROLE_ADMIN === $loggedInUser->getRole();

		$id = (int) $this->params()->fromRoute('id', 0);
		if (!$id) {
			return $this->redirect()->toRoute('flaminguser/user', array('action' => 'add'));
		}
		$user = $this->getUserService()->findUserById($id);

		if (!$user) {
			return $this->createHttpNotFoundModel($this->getResponse());
		}

		$form = $this->getUserForm();
		$form->setAdminMode($isAdmin);
		$form->getInputFilter()->setMode(UserFilter::FILTER_MODE_EDIT);
		$form->bind($user);

		$prg = $this->prg($this->url()->fromRoute('flaminguser/user', array(
			'action' => 'edit',
			'id' => $id
		)), true);

		if ($prg instanceof Response) {
			return $prg;
		} elseif ($prg === false) {
			return array(
				'id' => $id,
				'form' => $form,
				'isAdmin' => $isAdmin
			);
		}

		$form->setData($prg);

		if (!$form->isValid()) {
			return array(
				'id' => $id,
				'form' => $form,
				'isAdmin' => $isAdmin
			);
		}
		
		if ($this->getUserService()->updateUser($user)) {
			$this->flashMessenger()->addSuccessMessage('User updated');

			return $this->redirect()->toRoute('flaminguser/user');
		}
	}

	public function deleteAction()
	{
		/* @var $loggedInUser UserInterface */
		$loggedInUser = $this->authentication()->getIdentity();
		$isAdmin = UserInterface::ROLE_ADMIN === $loggedInUser->getRole();

		$id = (int) $this->params()->fromRoute('id', 0);
		if (!$id) {
			return $this->redirect()->toRoute('flaminguser/user');
		}

		$user = $this->getUserService()->findUserById($id);

		if (!$user) {
			return $this->createHttpNotFoundModel($this->getResponse());
		}

		$request = $this->getRequest();
		if ($request->isPost()) {
			$del = $request->getPost('del', 'No');

			if ('Yes' == $del) {
				$this->getUserService()->deleteUser($user);

				$this->flashMessenger()->addSuccessMessage('User deleted');
			}

			// Redirect to list of albums
			return $this->redirect()->toRoute('flaminguser/user');
		}

		$view = new ViewModel(array(
			'id' => $id,
			'user' => $user
		));

		if ($request->isXmlHttpRequest())
			$view->setTerminal(true);

		return $view;
	}

	/**
	 *
	 * @return UserService
	 */
	public function getUserService()
	{
		if (!$this->userService)
			$this->userService = $this->getServiceLocator()->get('FlamingUser\Service\UserService');
		return $this->userService;
	}

	/**
	 *
	 * @return FormInterface
	 */
	public function getUserForm()
	{
		if (!$this->userForm)
			$this->userForm = $this->getServiceLocator()->get('FlamingUser\Form\UserForm');
		return $this->userForm;
	}
}