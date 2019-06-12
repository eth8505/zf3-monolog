<?php declare(strict_types=1);

namespace Eth8505\Monolog\Formatter;

use Monolog\Formatter\FormatterInterface;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * Plugin manager for formatters
 */
class FormatterPluginManager extends AbstractPluginManager
{

    /**
     * @var string
     */
    protected $instanceOf = FormatterInterface::class;

}