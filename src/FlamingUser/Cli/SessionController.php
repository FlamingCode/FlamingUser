<?php

/*
 * Copyright (c) 2013, Flaming Code
 * 
 */

namespace FlamingUser\Cli;

use FlamingBase\Controller\AbstractCliController;

use Zend\Console\Request as ConsoleRequest;
use Zend\Db\TableGateway\TableGatewayInterface;

/**
 * SessionController
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2013, Flaming Code
 */
class SessionController extends AbstractCliController
{
	/**
	 *
	 * @var TableGatewayInterface
	 */
	protected $sessionTableGateway;

	protected $emailReceiver;

	public function cleanupAction()
	{
		$request = $this->getRequest();
		if (!$request instanceof ConsoleRequest)
			throw new \RuntimeException('You can only use this action from a console!');

		if (false === ($pid = $this->lock()))
			return;

		$table = $this->getSessionTableGateway();
		$deleteCount = $table->delete(sprintf('%s + %s < %d', 'modified',
		                                      'lifetime', time()));

		$output = "Deleted $deleteCount sessions\n";

		if (0 < $deleteCount && ($request->getParam('m') ||
		                         $request->getParam('send-mail'))) {
			$this->emailer()->sendMail($this->getEmailReceiver(),
			                           'FlamingUser [' . $this->env() . '] - session cleanup',
			                           $output);
		}

		$this->unlock();
		return $output;
	}

	public function clearAction()
	{
		$request = $this->getRequest();
		if (!$request instanceof ConsoleRequest)
			throw new \RuntimeException('You can only use this action from a console!');

		if (false === ($pid = $this->lock()))
			return;

		$table = $this->getSessionTableGateway();
		$deleteCount = $table->delete(array());

		$output = "Deleted $deleteCount sessions\n";

		$this->unlock();
		return $output;
	}

	/**
	 *
	 * @return TableGatewayInterface
	 */
	public function getSessionTableGateway()
	{
		if (!$this->sessionTableGateway) {
			$this->sessionTableGateway = $this->getServiceLocator()->get('flaminguser_session_tablegateway');
		}
		return $this->sessionTableGateway;
	}

	/**
	 *
	 * @param TableGatewayInterface $tableGateway
	 * @return SessionController
	 */
	public function setSessionTableGateway(TableGatewayInterface $tableGateway)
	{
		$this->sessionTableGateway = $tableGateway;
		return $this;
	}
	
	public function getEmailReceiver()
	{
		if (!$this->emailReceiver) {
			$config = $this->getServiceLocator()->get('Configuration');
			$this->emailReceiver = $config['flamingsms']['session_cleanup']['default_email_receiver'];
		}
		return $this->emailReceiver;
	}
}
