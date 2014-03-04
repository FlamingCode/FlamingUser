<?php

/*
 * Copyright (c) 2013, Flaming Code
 * 
 */

namespace FlamingUser\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Form\FormInterface;

use FlamingUser\Service\UserService;
use FlamingBase\Stdlib\StringTool;

/**
 * AuthController
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2013, Flaming Code
 * @link http://github.com/FlamingCode/FlamingUser for the canonical source repository
 * @license http://opensource.org/licenses/MIT MIT
 */
class AuthController extends AbstractActionController
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
	protected $loginForm;

	/**
	 *
	 * @var FormInterface
	 */
	protected $forgotPasswordForm;

	/**
	 *
	 * @var FormInterface
	 */
	protected $changePasswordForm;

	protected $successRoute;

	protected $failureRoute;

	protected $logoutRoute;

	protected $failedLoginMessage;

	/**
	 * Login form
	 */
	public function loginAction()
	{
		$request = $this->getRequest();
		$form = $this->getLoginForm();

		if (!$request->isPost()) {
			return array(
				'form' => $form,
				'query' => $this->params()->fromQuery()
			);
		}

		$form->setData($request->getPost());

		if (!$form->isValid()) {
			$this->flashMessenger()->addErrorMessage($this->getFailedLoginMessage());
			return $this->redirect()->toRoute($this->getFailureRoute(), array(), array('query' => $this->params()->fromQuery()));
		}

		return $this->authenticateAction();
	}

	/**
	 * Logout and clear the identity
	 */
	public function logoutAction()
	{
		$this->getUserService()->logout();
		
		if ($url = $this->params()->fromQuery('r', false))
			return $this->redirect()->toUrl($url);

		return $this->redirect()->toRoute($this->getLogoutRoute());
	}

	/**
	 * General-purpose authentication action
	 */
	public function authenticateAction()
	{
		$request = $this->getRequest();
		if ($this->authentication()->hasIdentity())
			return $this->redirect()->toRoute($this->getSuccessRoute());
		else if (!$request->isPost())
			return $this->redirect()->toRoute($this->getFailureRoute(), array(), array('query' => $this->params()->fromQuery()));

		$data = $request->getPost();
		if (!$this->getUserService()->login($data['email'], $data['password'],
		    (bool) $data['remember_me'])) {
			$this->flashMessenger()->addErrorMessage($this->getFailedLoginMessage());
			return $this->redirect()->toRoute($this->getFailureRoute(), array(), array('query' => $this->params()->fromQuery()));
		}
		
		if ($url = $this->params()->fromQuery('r', false))
			return $this->redirect()->toUrl($url);

		return $this->redirect()->toRoute($this->getSuccessRoute());
	}

	public function forgotPasswordAction()
	{
		if ($this->authentication()->hasIdentity())
			return $this->redirect()->toRoute('home');

		$request = $this->getRequest();
		$form = $this->getForgotPasswordForm();

		$prg = $this->prg($this->url()->fromRoute('flaminguser/forgot-password'), true);

		if ($prg instanceof Response) {
			return $prg;
		} elseif ($prg === false) {
			return array(
				'form' => $form
			);
		}

		$form->setData($prg);
		if (!$form->isValid()) {
			return array(
				'form' => $form
			);
		}

		$data = $form->getData();
		$user = $this->getUserService()->findUserByEmail($data['email']);
		if ($user) {
			$forgotHash = StringTool::randStr();
			$url = $this->url()->fromRoute('flaminguser/change-password', array(), array(
				'query' => array(
					'id' => $forgotHash
				),
				'force_canonical' => true
			));

			$msgBody = <<<EOF
Hi {$user->getFullName()}

You have requested a link to change your password.

Click on this link to choose a new password: {$url}

If you haven't requested this simply ignore this email.
EOF;

			$this->emailer()->sendMail($user->getEmail(), 'Change password',
			$msgBody);

			$user->setForgotPassHash($forgotHash);
			$this->getUserService()->updateUser($user);
		}

		$this->flashMessenger()->addInfoMessage('Email sent');

		return $this->redirect()->toRoute('flaminguser/forgot-password');
	}

	public function changePasswordAction()
	{
		if ($this->authentication()->hasIdentity())
			return $this->redirect()->toRoute('home');

		$request = $this->getRequest();

		$params = $request->getQuery();
		if (null === $params['id'] ||
		    !($user = $this->getUserService()->findUserByForgotPassHash($params['id']))) {
			return $this->createHttpNotFoundModel($this->getResponse());
		}
		
		$form = $this->getChangePasswordForm();
		$form->bind($user);

		$prg = $this->prg($this->url()->fromRoute('flaminguser/change-password', array(), array(
			'query' => array(
				'id' => $params['id']
			)
		)), true);

		if ($prg instanceof Response) {
			return $prg;
		} elseif ($prg === false) {
			return array(
				'form' => $form,
				'id' => $params['id']
			);
		}

		$form->setData($prg);
		if (!$form->isValid()) {
			return array(
				'form' => $form,
				'id' => $params['id']
			);
		}

		$user->setForgotPassHash(null);
		
		$this->getUserService()->updateUser($user);

		$this->flashMessenger()->addInfoMessage('You can now login using your new password');

		return $this->redirect()->toRoute('flaminguser/login');
	}

	public function getUserService()
	{
		if (!$this->userService) {
			$this->userService = $this->getServiceLocator()->get('FlamingUser\Service\UserService');
		}
		return $this->userService;
	}

	public function setUserService(UserService $userService)
	{
		$this->userService = $userService;
		return $this;
	}

	public function getLoginForm()
	{
		if (!$this->loginForm)
			$this->setLoginForm($this->getServiceLocator()->get('FlamingUser\Form\LoginForm'));
		return $this->loginForm;
	}

	public function setLoginForm(FormInterface $loginForm)
	{
		$this->loginForm = $loginForm;
		return $this;
	}

	public function getForgotPasswordForm()
	{
		if (!$this->forgotPasswordForm)
			$this->setForgotPasswordForm($this->getServiceLocator()->get('FlamingUser\Form\ForgotPasswordForm'));
		return $this->forgotPasswordForm;
	}

	public function setForgotPasswordForm(FormInterface $forgotPasswordForm)
	{
		$this->forgotPasswordForm = $forgotPasswordForm;
		return $this;
	}

	public function getChangePasswordForm()
	{
		if (!$this->changePasswordForm)
			$this->setChangePasswordForm($this->getServiceLocator()->get('FlamingUser\Form\ChangePasswordForm'));
		return $this->changePasswordForm;
	}

	public function setChangePasswordForm(FormInterface $changePasswordForm)
	{
		$this->changePasswordForm = $changePasswordForm;
		return $this;
	}

	public function getSuccessRoute()
	{
		if (!$this->successRoute) {
			$config = $this->getServiceLocator()->get('Configuration');
			$this->successRoute = $config['flaminguser']['authentication']['success_route'];
		}
		return $this->successRoute;
	}

	public function getFailureRoute()
	{
		if (!$this->failureRoute) {
			$config = $this->getServiceLocator()->get('Configuration');
			$this->failureRoute = $config['flaminguser']['authentication']['failure_route'];
		}
		return $this->failureRoute;
	}

	public function getLogoutRoute()
	{
		if (!$this->logoutRoute) {
			$config = $this->getServiceLocator()->get('Configuration');
			$this->logoutRoute = $config['flaminguser']['authentication']['logout_route'];
		}
		return $this->logoutRoute;
	}

	public function getFailedLoginMessage()
	{
		if (!$this->failedLoginMessage) {
			$config = $this->getServiceLocator()->get('Configuration');
			$this->failedLoginMessage = $config['flaminguser']['authentication']['failed_login_message'];
		}
		return $this->failedLoginMessage;
	}
}