<?php declare(strict_types=1);

namespace Eth8505\Monolog\Factory;

use Eth8505\Monolog\Exception\RuntimeException;
use Eth8505\Monolog\MonologOptions;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

/**
 * Abstract logger factory to create arbitrary loggers
 */
class LoggerAbstractFactory implements AbstractFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return $this->getLoggerConfig($container->get('Config')['monolog'], $requestedName) !== null;
    }

    /**
     * Get logger config from config
     *
     * @param array $config
     * @param string $requestedName
     * @return MonologOptions|null
     */
    private function getLoggerConfig(array $config, string $requestedName): ?MonologOptions
    {

        if (!isset($config['loggers']) || !is_array($config['loggers'])) {
            return null;
        }

        $loggers = $config['loggers'];

        if (!isset($loggers[$requestedName]) || !is_array($loggers[$requestedName])) {
            return null;
        }

        return new MonologOptions($loggers[$requestedName]);

    }

    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {

        $loggerFactory = $container->get(LoggerFactory::class);
        $loggerConfig = $this->getLoggerConfig($container->get('Config')['monolog'], $requestedName);

        if ($loggerConfig === null) {
            throw new RuntimeException("Logger config for \"{$requestedName}\" not found");
        }

        return $loggerFactory($loggerConfig);

    }

}