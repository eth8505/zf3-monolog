<?php declare(strict_types=1);

namespace Eth8505\Monolog\Formatter\Factory;

use Eth8505\Monolog\Handler\FormatterPluginManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class FormatterPluginManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new FormatterPluginManager(
            $container,
            $container->get('Config')['monolog']['formatters'] ?? []
        );
    }

}