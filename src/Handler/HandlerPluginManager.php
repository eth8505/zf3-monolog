<?php declare(strict_types=1);

namespace Eth8505\Monolog\Handler;

use Monolog\Handler\HandlerInterface;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * Plugin manager for handlers
 */
class HandlerPluginManager extends AbstractPluginManager
{

    /**
     * @var string
     */
    protected $instanceOf = HandlerInterface::class;

}