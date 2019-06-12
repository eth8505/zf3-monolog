<?php use Eth8505\Monolog\Factory\Factory\LoggerFactoryFactory;
use Eth8505\Monolog\Factory\LoggerAbstractFactory;
use Eth8505\Monolog\Factory\LoggerFactory;
use Eth8505\Monolog\Formatter\Factory\FormatterPluginManagerFactory;
use Eth8505\Monolog\Formatter\FormatterPluginManager;
use Eth8505\Monolog\Handler\Factory\HandlerPluginManagerFactory;
use Eth8505\Monolog\Handler\HandlerPluginManager;
use Eth8505\Monolog\Processor\Factory\ProcessorPluginManagerFactory;
use Eth8505\Monolog\Processor\ProcessorPluginManager;

return [

    'service_manager' => [
        'factories' => [
            LoggerFactory::class => LoggerFactoryFactory::class,
            HandlerPluginManager::class => HandlerPluginManagerFactory::class,
            FormatterPluginManager::class => FormatterPluginManagerFactory::class,
            ProcessorPluginManager::class => ProcessorPluginManagerFactory::class
        ],
        'abstract_factories' => [
            LoggerAbstractFactory::class
        ]
    ],

    'monolog' => [
        'formatters' => [],
        'handlers' => [],
        'processors' => []
    ]

];