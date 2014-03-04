<?php

/*
 * Copyright (c) 2013, Flaming Code
 * 
 */

namespace FlamingUser;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Console\Adapter\AdapterInterface as Console;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

/**
 * Module
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2013, Flaming Code
 * @link http://github.com/FlamingCode/FlamingUser for the canonical source repository
 * @license http://opensource.org/licenses/MIT MIT
 */
class Module implements ConsoleUsageProviderInterface
{
	public function getServiceConfig()
	{
		return array(
			'factories' => array(
				'FlamingUser\InputFilter\UserFilter' => function($sm) {
					$config = $sm->get('Configuration');
					$entityMgr = $sm->get('Doctrine\ORM\EntityManager');
					return new InputFilter\UserFilter($entityMgr, $config['flaminguser']['user_service']['user_entity']);
				},
				
				/*
				 * Forms
				 */
				'FlamingUser\Form\LoginForm' => function($sm) {
					$form = new Form\LoginForm;
					$form->setInputFilter(new InputFilter\LoginFilter);
					return $form;
				},
				'FlamingUser\Form\ForgotPasswordForm' => function($sm) {
					$form = new Form\ForgotPasswordForm;
					$form->setInputFilter(new InputFilter\ForgotPasswordFilter);
					return $form;
				},
				'FlamingUser\Form\ChangePasswordForm' => function($sm) {
					$config = $sm->get('Configuration');
					$form = new Form\ChangePasswordForm;
					$form->setObject(new $config['flaminguser']['user_service']['user_entity'])
					     ->setHydrator($sm->get('FlamingUser\Hydrator\UserHydrator'))
					     ->setInputFilter(new InputFilter\ChangePasswordFilter);
					return $form;
				},
				'FlamingUser\Form\UserForm' => function($sm) {
					$config = $sm->get('Configuration');
					$entityMgr = $sm->get('Doctrine\ORM\EntityManager');
					$form = new Form\UserForm;
					$form->setObject(new $config['flaminguser']['user_service']['user_entity'])
					     ->setHydrator($sm->get('FlamingUser\Hydrator\UserHydrator'))
					     ->setInputFilter($sm->get('FlamingUser\InputFilter\UserFilter'));
					return $form;
				},
				'FlamingUser\Form\ProfileForm' => function($sm) {
					$config = $sm->get('Configuration');
					$entityMgr = $sm->get('Doctrine\ORM\EntityManager');
					$form = new Form\ProfileForm;
					$form->setObject(new $config['flaminguser']['user_service']['user_entity'])
					     ->setHydrator($sm->get('FlamingUser\Hydrator\UserHydrator'))
					     ->setInputFilter(new InputFilter\ProfileFilter($entityMgr, $config['flaminguser']['user_service']['user_entity']));
					return $form;
				},

				'FlamingUser\Service\UserService' => function($sm) {
					$config = $sm->get('Configuration');
					$service = new Service\UserService;
					$service->setEntityManager($sm->get('Doctrine\ORM\EntityManager'))
					        ->setAuthService($sm->get('flaminguser_auth_service'))
					        ->setHydrator($sm->get('FlamingUser\Hydrator\UserHydrator'))
					        ->setOptions($config['flaminguser']['user_service']);
					return $service;
				},

				'FlamingUser\Hydrator\UserHydrator' => function($sm) {
					$config = $sm->get('Configuration');
					$hydrator = new DoctrineHydrator($sm->get('Doctrine\ORM\EntityManager'), $config['flaminguser']['user_service']['user_entity']);
					return $hydrator;
				},

				/*
				 * Authorization service
				 *
				 * Consumes ACL and authentication service
				 * NOTE: Not used directly, but through the controller plugin or UserService
				 */
				'FlamingUser\Service\AuthorizationService' => function($sm) {
					$config = $sm->get('Configuration');
					$service = new Service\AuthorizationService;
					$service->setAuthService($sm->get('flaminguser_auth_service'))
					        ->setAcl($sm->get('FlamingUser\Permissions\Acl\Acl'))
					        ->setRedirectRoute($config['flaminguser']['authorization']['redirect_route'])
					        ->setLoginRoute($config['flaminguser']['authorization']['login_route']);
					return $service;
				},

				'FlamingUser\Permissions\Acl\Acl' => function($sm) {
					$config = $sm->get('Configuration');
					$acl = new Permissions\Acl\Acl($config['acl']);
					return $acl;
				},

				/*
				 * Authentication service
				 */
				'flaminguser_auth_service' => function($sm) {
					$authAdapter = $sm->get('doctrine.authenticationadapter.orm_default');
					$storage = new Authentication\Storage\DoctrineEntity(
						$sm->get('Doctrine\ORM\EntityManager'),
						null,
						null,
						\Zend\Session\Container::getDefaultManager()
					);
					$authService = new \Zend\Authentication\AuthenticationService();
					$authService->setAdapter($authAdapter);
					$authService->setStorage($storage);
					return $authService;
				},

				/*
				 * Session in DB
				 */
				'flaminguser_session_tablegateway' => function($sm) {
					$config = $sm->get('Configuration');
					$dbAdapter = new \Zend\Db\Adapter\Adapter($config['session_db']);
					return new \Zend\Db\TableGateway\TableGateway('sessions', $dbAdapter);
				},
			),
		);
	}

	public function getViewHelperConfig()
	{
		return array(
			'factories' => array(
				'authentication' => function ($helpers) {
					$sm = $helpers->getServiceLocator();
					$plugin = $sm->get('ControllerPluginManager')->get('authentication');
					return new View\Helper\Authentication($plugin);
				},
			)
		);
	}

	public function getControllerPluginConfig()
	{
		return array(
			'factories' => array(
				'authentication' => function($helpers) {
					$serviceLocator = $helpers->getServiceLocator();
					$authService = $serviceLocator->get('flaminguser_auth_service');
					$controllerPlugin = new Controller\Plugin\Authentication;
					$controllerPlugin->setService($authService);
					return $controllerPlugin;
				},
			),
		);
	}
	
	public function getConsoleUsage(Console $console)
	{
		return array(
			'Session Cleanup',
			'session cleanup [-m|--send-mail]' => 'Session cleanup',
			'session clear' => 'Delete all sessions',

			'User Management',
			'user add [-g|--generate-password] [--password=] <email> [role] [firstname] [surname]' => 'Add a user to the system. The default role is \'user\'.',
		);
	}

	public function onBootstrap(MvcEvent $e)
	{
		/* @var $application \Zend\Mvc\Application */
		$application = $e->getApplication();

		/* @var $eventManager \Zend\EventManager\EventManager */
		$eventManager = $application->getEventManager();
		$moduleRouteListener = new ModuleRouteListener();
		$moduleRouteListener->attach($eventManager);

		/* @var $serviceManager \Zend\ServiceManager\ServiceManager */
		$serviceManager = $application->getServiceManager();

		// Ignore cli as we don't want to start a new session and check the ACL for this
		if (php_sapi_name() != 'cli') {
			$this->bootstrapSession($serviceManager);

			$authorization = $serviceManager->get('FlamingUser\Service\AuthorizationService');

			$this->bootstrapNavigation($authorization);

			// Attach to dispatch event so we can capture and evaluate permissions
			$sharedEventManager = $eventManager->getSharedManager();
			$sharedEventManager->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', array($authorization, 'onPreDispatch'), 99);
		}
	}

	protected function bootstrapNavigation($authorizationService)
	{
		\Zend\View\Helper\Navigation::setDefaultAcl($authorizationService->getAcl());
		\Zend\View\Helper\Navigation::setDefaultRole($authorizationService->getRole());
	}

	protected function bootstrapSession(\Zend\ServiceManager\ServiceManager $sm)
	{
		$config = $sm->get('Configuration');
		$saveHandler = null;
		if ($config['flaminguser']['use_session_db']) {
			$sessionTableGateway = $sm->get('flaminguser_session_tablegateway');
			$sessionTableGWOptions = new \Zend\Session\SaveHandler\DbTableGatewayOptions;
			$saveHandler = new \Zend\Session\SaveHandler\DbTableGateway($sessionTableGateway,
										    $sessionTableGWOptions);
		}

		$sessionConfig = new \Zend\Session\Config\SessionConfig();
		$sessionConfig->setOptions($config['session']);
		$sessionManager = new \Zend\Session\SessionManager($sessionConfig, null,
		                                                   $saveHandler);
		$sessionManager->start();
		\Zend\Session\Container::setDefaultManager($sessionManager);
	}

	public function getConfig()
	{
		return include __DIR__ . '/config/module.config.php';
	}

	public function getAutoloaderConfig()
	{
		return array(
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
				),
			),
		);
	}
}
