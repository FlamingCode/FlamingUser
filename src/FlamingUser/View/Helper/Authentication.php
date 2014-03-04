<?php

/*
 * Copyright (c) 2013, Flaming Code
 * 
 */

namespace FlamingUser\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Mvc\Controller\Plugin\PluginInterface;

/**
 * Authentication
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2013, Flaming Code
 * @link http://github.com/FlamingCode/FlamingUser for the canonical source repository
 * @license http://opensource.org/licenses/MIT MIT
 */
class Authentication extends AbstractHelper
{
	protected $plugin;

	public function __construct(PluginInterface $plugin = null)
	{
		if ($plugin)
			$this->setPlugin($plugin);
	}

	public function __invoke()
	{
		return $this->getPlugin();
	}

	public function getPlugin()
	{
		return $this->plugin;
	}

	public function setPlugin(PluginInterface $plugin)
	{
		$this->plugin = $plugin;
		return $this;
	}
}