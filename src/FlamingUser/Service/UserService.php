<?php

/*
 * Copyright (c) 2013, Flaming Code
 * 
 */

namespace FlamingUser\Service;

use FlamingUser\Entity\User as UserEntity;
use FlamingUser\Entity\UserInterface;
use FlamingBase\Stdlib\StringTool;
use FlamingBase\Service\AbstractService;

use Zend\Crypt\Password\Bcrypt;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result as AuthResult;
use Zend\Session\Container as SessionContainer;

/**
 * UserService
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2013, Flaming Code
 */
class UserService extends AbstractService
{
	const DEFAULT_PASS_COST = 14;

	/**
	 *
	 * @var AuthenticationService
	 */
	protected $authService;

	/**
	* Find a record by id
	*
	* @param int $id
	* @return object FlamingUser\Entity\User
	**/
	public function findUserById($id)
	{
		return $this->getEntityManager()->find('FlamingUser\Entity\User', $id);
	}

	/**
	 *
	 * @param string $hash
	 * @return UserEntity|null
	 */
	public function findUserByForgotPassHash($hash)
	{
		return $this->getEntityManager()->getRepository('FlamingUser\Entity\User')
		                                ->findOneBy(array(
			'forgotPassHash' => $hash
		));
	}

	/**
	 *
	 * @param string $email
	 * @return UserEntity|null
	 */
	public function findUserByEmail($email)
	{
		return $this->getEntityManager()->getRepository('FlamingUser\Entity\User')
		                                ->findOneBy(array(
			'email' => $email
		));
	}

	public function findUsers($isAdmin = false)
	{
		return $this->getEntityManager()->getRepository('FlamingUser\Entity\User')
		                                ->findAll($isAdmin);
	}

	public function createUser(array $data)
	{
		if (array_key_exists('passwordVerify', $data))
			unset($data['passwordVerify']);

		$data['password'] = self::hashPassword((string) $data['password']);

		$user = $this->getHydrator()->hydrate($data, new UserEntity);

		// Save the record
		$this->getEntityManager()->persist($user);
		$this->getEntityManager()->flush();

		return $user;
	}

	public function updateUser(UserEntity $user, array $data = array())
	{
		if (array_key_exists('passwordVerify', $data))
			unset($data['passwordVerify']);

		if (array_key_exists('password', $data) && $data['password'])
			$data['password'] = self::hashPassword((string) $data['password']);
		else if (array_key_exists('password', $data))
			unset($data['password']);

		$user = $this->getHydrator()->hydrate($data, $user);

		// Save the record
		$this->getEntityManager()->persist($user);
		$this->getEntityManager()->flush();

		return $user;
	}

	public function deleteUser(UserEntity $user)
	{
		$this->getEntityManager()->remove($user);
		$this->getEntityManager()->flush();

		// TODO: What should we return?
		return;
	}

	public function login($email, $password, $rememberMe = false)
	{
		$sessionManager = SessionContainer::getDefaultManager();

		// Default TTL is two weeks
		if ($rememberMe)
			$sessionManager->rememberMe();
		else
			$sessionManager->forgetMe();

		$adapter = $this->getAuthService()->getAdapter();
		$adapter->setIdentityValue($email);
		$adapter->setCredentialValue($password);

		$result = $this->getAuthService()->authenticate();

		// TODO: Here we can log all the attempts
		switch ($result->getCode()) {
			// Success
			case AuthResult::SUCCESS:
				break;

			// General error
			case AuthResult::FAILURE:
				break;

			// Couldn't find email
			case AuthResult::FAILURE_IDENTITY_NOT_FOUND:
				break;

			// Invalid password
			case AuthResult::FAILURE_CREDENTIAL_INVALID:
				break;

			// This should never happen
			case AuthResult::FAILURE_IDENTITY_AMBIGUOUS:
				break;

			// Unknown error
			case AuthResult::FAILURE_UNCATEGORIZED:
				break;
		}

		$isValid = $result->isValid();
//		$events = $this->getEventManager();
		if ($isValid) {
//			$events->trigger(UserEvent::EVENT_USER_LOGIN_SUCCESS, $this, array(
//				'user' => $this->getAuthService()->getIdentity(),
//				'identity' => $result->getIdentity(),
//				'result' => $result
//			));
		} else {
//			$events->trigger(UserEvent::EVENT_USER_LOGIN_FAIL, $this, array(
//				'identity' => $result->getIdentity(),
//				'result' => $result
//			));
		}

		return $isValid;
	}

	public function logout()
	{
		if (!$this->getAuthService()->hasIdentity())
			return false;

//		$events = $this->getEventManager();
//		$events->trigger(UserEvent::EVENT_USER_LOGOUT, $this, array(
//			'user' => $this->getAuthService()->getIdentity()
//		));

		$this->getAuthService()->clearIdentity();
		$sessionManager = SessionContainer::getDefaultManager();
		$sessionManager->destroy();
		return true;
	}

	public static function hashSha256($str)
	{
		return hash('sha256', $str);
	}

	public static function hashPassword($str)
	{
		$bcrypt = new Bcrypt;
		$bcrypt->setCost(self::DEFAULT_PASS_COST);
		return $bcrypt->create($str);
	}

	public static function checkPassword($passwordHashed, $passwordGiven)
	{
		$bcrypt = new Bcrypt;
		$bcrypt->setCost(self::DEFAULT_PASS_COST);
		return $bcrypt->verify($passwordGiven, $passwordHashed);
	}

	/**
	 *
	 * @return AuthenticationService
	 */
	public function getAuthService()
	{
		return $this->authService;
	}

	/**
	 *
	 * @param AuthenticationService $authService
	 * @return UserService
	 */
	public function setAuthService(AuthenticationService $authService)
	{
		$this->authService = $authService;
		return $this;
	}
}