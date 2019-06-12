<?php declare(strict_types=1);

namespace Eth8505\Monolog\Factory;

use Eth8505\Monolog\Handler\FormatterPluginManager;
use Eth8505\Monolog\Handler\HandlerPluginManager;
use Eth8505\Monolog\MonologOptions;
use Eth8505\Monolog\Processor\ProcessorPluginManager;
use Interop\Container\ContainerInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class LoggerFactory
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var HandlerPluginManager
     */
    private $handlerPluginManager;

    /**
     * @var ProcessorPluginManager
     */
    private $processorPluginManager;

    /**
     * @var FormatterPluginManager
     */
    private $formatterPluginManager;

    /**
     * Constructor
     * @param ContainerInterface $container
     * @param HandlerPluginManager $handlerPluginManager
     * @param ProcessorPluginManager $processorPluginManager
     * @param FormatterPluginManager $formatterPluginManager
     */
    public function __construct(
        ContainerInterface $container,
        HandlerPluginManager $handlerPluginManager,
        ProcessorPluginManager $processorPluginManager,
        FormatterPluginManager $formatterPluginManager
    ) {
        $this->container = $container;
        $this->handlerPluginManager = $handlerPluginManager;
        $this->processorPluginManager = $processorPluginManager;
        $this->formatterPluginManager = $formatterPluginManager;
    }

    /**
     * Create logger
     *
     * @param MonologOptions $options
     * @return LoggerInterface
     */
    public function __invoke(MonologOptions $options): LoggerInterface
    {

        $logger = new Logger($options->getName());

        foreach ($options->getHandlers() as $handlerConfig) {

            if (!is_array($handlerConfig)) {
                $handlerConfig = ['class' => $handlerConfig];
            }

            $logger->pushHandler($this->createHandler($handlerConfig));
        }

        foreach ($options->getProcessors() as $processorConfig) {

            if (!is_array($processorConfig)) {
                $processorConfig = ['class' => $processorConfig];
            }

            $logger->pushProcessor(
                $this->processorPluginManager->get($processorConfig['class'], $processorConfig['options'] ?? [])
            );

        }

        return $logger;


    }

    /**
     * Create handler from config
     *
     * @param array $handlerConfig
     * @return HandlerInterface
     */
    private function createHandler(array $handlerConfig): HandlerInterface
    {

        $handler = $this->handlerPluginManager->get($handlerConfig['class'], $handlerConfig['options'] ?? []);

        if (isset($handlerConfig['formatter'])) {

            $formatterConfig = is_array($handlerConfig['formatter'])
                ? $handlerConfig['formatter']
                : ['class' => $handlerConfig['formatter']];

            $formatter = $this->formatterPluginManager->get(
                $formatterConfig['class'],
                $formatterConfig['options'] ?? []
            );

            $handler->setFormatter($formatter);

        }

        return $handler;

    }

}