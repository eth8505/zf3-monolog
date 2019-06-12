<?php declare(strict_types=1);

namespace Eth8505\Monolog\Factory\Factory;

use Eth8505\Monolog\Factory\LoggerFactory;
use Eth8505\Monolog\Formatter\FormatterPluginManager;
use Eth8505\Monolog\Handler\HandlerPluginManager;
use Eth8505\Monolog\Processor\ProcessorPluginManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class LoggerFactoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new LoggerFactory(
            $container,
            $container->get(HandlerPluginManager::class),
            $container->get(ProcessorPluginManager::class),
            $container->get(FormatterPluginManager::class)
        );
    }

}