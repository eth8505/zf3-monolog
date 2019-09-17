<?php declare(strict_types=1);

namespace Eth8505\Monolog\Factory;

use Eth8505\Monolog\Exception\InvalidArgumentException;
use Eth8505\Monolog\Exception\OutOfBoundsException;
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
     * @var int
     */
    private const INHERITANCE_LEVEL_LIMIT = 10;

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

        $loggerConfig = $loggers[$requestedName];

        if (isset($loggerConfig['@extends'])) {

            $recursionDepth = 0;

            do {

                if (($recursionDepth + 1) > self::INHERITANCE_LEVEL_LIMIT) {
                    throw new RuntimeException(sprintf('Maximum inheritance level of %u reached', self::INHERITANCE_LEVEL_LIMIT));
                } elseif (!is_string($loggerConfig['@extends'])) {
                    throw new InvalidArgumentException('@extends must be string');
                } elseif (!isset($loggers[$loggerConfig['@extends']])) {
                    throw new OutOfBoundsException("Offset {$loggerConfig['@extends']} does not exist");
                }

                $nextConfig = $loggers[$loggerConfig['@extends']];
                unset($loggerConfig['@extends']);
                $loggerConfig = array_replace_recursive($nextConfig, $loggerConfig);
                $recursionDepth++;

            } while (isset($loggerConfig['@extends']));

        }

        return new MonologOptions($loggerConfig);

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