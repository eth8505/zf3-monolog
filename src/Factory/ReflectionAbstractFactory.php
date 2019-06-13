<?php declare(strict_types=1);

namespace Eth8505\Monolog\Factory;

use Eth8505\Monolog\Exception\ClassNotFoundException;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

/**
 * Factory to generically create instances via named parameters using reflection
 */
class ReflectionAbstractFactory implements AbstractFactoryInterface
{

    /**
     * @inheritDoc
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return true;
    }

    /**
     * @inheritDoc
     * @throws \ReflectionException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {

        if (!class_exists($requestedName)) {
            throw new ClassNotFoundException($requestedName);
        }

        $reflectionClass = new \ReflectionClass($requestedName);
        $constructorArgs = $reflectionClass->getMethod('__construct')->getParameters();

        $args = [];

        foreach ($constructorArgs as $constructorArg) {
            $args[] = isset($options[$constructorArg->getName()]) ? $constructorArg->getDefaultValue() : null;
        }

        return $reflectionClass->newInstanceArgs($args);

    }

}