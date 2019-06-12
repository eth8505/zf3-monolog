<?php declare(strict_types=1);

namespace Eth8505\Monolog\Processor;

use Monolog\Processor\ProcessorInterface;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * Plugin manager for processors
 */
class ProcessorPluginManager extends AbstractPluginManager
{

    /**
     * @var string
     */
    protected $instanceOf = ProcessorInterface::class;

}