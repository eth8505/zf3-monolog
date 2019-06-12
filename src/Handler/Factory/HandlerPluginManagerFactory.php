<?php declare(strict_types=1);

namespace Eth8505\Monolog\Handler\Factory;

use Eth8505\Monolog\Handler\HandlerPluginManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class HandlerPluginManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new HandlerPluginManager(
            $container,
            $container->get('Config')['monolog']['handlers'] ?? []
        );
    }

}