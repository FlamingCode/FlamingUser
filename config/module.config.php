<?php

/*
 * Copyright (c) 2013, Flaming Code
 * 
 */

namespace FlamingUser;

return array(
	'router' => array(
		'routes' => array(
			'flaminguser' => array(
				'type' => 'Literal',
				'options' => array(
					'route' => '/user',
					'defaults' => array(
						'__NAMESPACE__' => 'FlamingUser\Controller',
						'controller' => 'Index',
						'action' => 'index',
					),
				),
				'may_terminate' => true,
				'child_routes' => array(
					'login' => array(
						'type' => 'literal',
						'options' => array(
							'route' => '/login',
							'defaults' => array(
								'controller' => 'Auth',
								'action' => 'login'
							)
						)
					),

					'authenticate' => array(
						'type' => 'literal',
						'options' => array(
							'route' => '/authenticate',
							'defaults' => array(
								'controller' => 'Auth',
								'action' => 'authenticate'
							)
						)
					),

					'logout' => array(
						'type' => 'literal',
						'options' => array(
							'route' => '/logout',
							'defaults' => array(
								'controller' => 'Auth',
								'action' => 'logout'
							)
						)
					),

					'forgot-password' => array(
						'type' => 'literal',
						'options' => array(
							'route' => '/forgot-password',
							'defaults' => array(
								'controller' => 'Auth',
								'action' => 'forgot-password'
							)
						)
					),

					'change-password' => array(
						'type' => 'literal',
						'options' => array(
							'route' => '/change-password',
							'defaults' => array(
								'controller' => 'Auth',
								'action' => 'change-password'
							)
						)
					),

					'user' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/users[/:action][/:id]',
							'constraints' => array(
								'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
								'id' => '[0-9]+'
							),
							'defaults' => array(
								'controller' => 'FlamingUser\Controller\User',
								'action' => 'index'
							)
						)
					),
				),
			),
		)
	),

	'controllers' => array(
		'invokables' => array(
			'FlamingUser\Controller\Auth' => 'FlamingUser\Controller\AuthController',
			'FlamingUser\Controller\User' => 'FlamingUser\Controller\UserController'
		)
	),

	'flaminguser' => array(
		'user_service' => array(
			'user_entity' => 'FlamingUser\Entity\User'
		),
		
		'authorization' => array(
			'redirect_route' => 'home',
			'login_route' => 'flaminguser/login'
		),

		'authentication' => array(
			'success_route' => 'home',
			'failure_route' => 'flaminguser/login',
			'logout_route' => 'home',
			'failed_login_message' => 'Wrong email or password. Please try again.'
		),
		
		// Use session db by default
		'use_session_db' => true,
	),

	'acl' => array(
		'roles' => array(
			'guest' => null,
			'user' => 'guest',
			'admin' => 'user'
		),
		'resources' => array(
			'allow' => array(
				'flaminguser/login' => array(
					'login' => 'guest'
				),

				'flaminguser/authenticate' => array(
					'authenticate' => 'guest'
				),

				'flaminguser/forgot-password' => array(
					'forgot-password' => 'guest'
				),

				'flaminguser/change-password' => array(
					'change-password' => 'guest'
				),

				'flaminguser/logout' => array(
					'logout' => 'user'
				),

				'flaminguser/user' => array(
					'*' => 'admin'
				)
			),
			'deny' => array(
				'flaminguser/login' => array(
					'login' => 'user'
				),
			)
		)
	),

	// Doctrine config
	'doctrine' => array(
		'driver' => array(
			__NAMESPACE__ . '_driver' => array(
				'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
				'cache' => 'array',
				'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity')
			),
			'orm_default' => array(
				'drivers' => array(
					__NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
				)
			)
		),

		'authentication' => array(
			'orm_default' => array(
				'object_manager' => 'Doctrine\ORM\EntityManager',
				'identity_class' => 'FlamingUser\Entity\User',
				'identity_property' => 'email',
				'credential_property' => 'password',
				'credential_callable' => function(Entity\UserInterface $user, $passwordGiven) {
					return Service\UserService::checkPassword($user->getPassword(), $passwordGiven) &&
					       $user->getActive() && $user->getEmailConfirmed();
				},
			),
		),
	),

	'session' => array(
		'name' => 'flaminguser_session',
		'remember_me_seconds' => 14 * 24 * 60 * 60,
		'use_cookies' => true,
		'cookie_httponly' => true,
//		'cookie_lifetime' => 14 * 24 * 60 * 60,
//		'gc_maxlifetime' => 14 * 24 * 60 * 60,
		// Only affects session storage in files
		'save_path' => '/tmp'
        ),

	'view_manager' => array(
		'template_path_stack' => array(
			'flaming-user' => __DIR__ . '/../view'
		),
	),
);