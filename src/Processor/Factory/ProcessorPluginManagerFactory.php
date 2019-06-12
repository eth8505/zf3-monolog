<?php declare(strict_types=1);

namespace Eth8505\Monolog\Processor\Factory;

use Eth8505\Monolog\Processor\ProcessorPluginManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ProcessorPluginManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new ProcessorPluginManager(
            $container,
            $container->get('Config')['monolog']['processors'] ?? []
        );
    }

}