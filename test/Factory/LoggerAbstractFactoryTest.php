<?php

namespace Factory;

use Eth8505\Monolog\Exception\InvalidArgumentException;
use Eth8505\Monolog\Exception\OutOfBoundsException;
use Eth8505\Monolog\Exception\RuntimeException;
use Eth8505\Monolog\Factory\LoggerAbstractFactory;
use Eth8505\Monolog\Factory\LoggerFactory;
use Eth8505\Monolog\Factory\ReflectionAbstractFactory;
use Eth8505\Monolog\Formatter\FormatterPluginManager;
use Eth8505\Monolog\Handler\HandlerPluginManager;
use Eth8505\Monolog\Processor\ProcessorPluginManager;
use Monolog\Formatter\ChromePHPFormatter;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\ProcessIdProcessor;
use PHPUnit\Framework\TestCase;
use Zend\ServiceManager\ServiceManager;

class LoggerAbstractFactoryTest extends TestCase
{

    public function testMergedRecursion(): void
    {

        $container = new ServiceManager();

        $container->setService('Config', [
            'monolog' => [
                'loggers' => [
                    'default' => [
                        'name' => 'default'
                    ],
                    'inherited' => [
                        'name' => 'inherited'
                    ]
                ]
            ]
        ]);

        $container->setService(LoggerFactory::class, new LoggerFactory(
            $container,
            new HandlerPluginManager($container, []),
            new ProcessorPluginManager($container, []),
            new FormatterPluginManager($container, [])
        ));

        $factory = new LoggerAbstractFactory();
        self::assertEquals('inherited', $factory($container, 'inherited')->getName());

    }

    public function testFailsIfConfigNotFound(): void
    {

        $container = new ServiceManager();
        $container->setService('Config', [
            'monolog' => [
                'loggers' => []
            ]
        ]);
        $container->setService(LoggerFactory::class, new LoggerFactory(
            $container,
            new HandlerPluginManager($container, []),
            new ProcessorPluginManager($container, []),
            new FormatterPluginManager($container, [])
        ));

        $factory = new LoggerAbstractFactory();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Logger config for "invalid" not found');
        $factory($container, 'invalid')->getName();

    }

    public function testFailsIfInheritTargetDoesNotExist(): void {

        $container = new ServiceManager();
        $container->setService('Config', [
            'monolog' => [
                'loggers' => [
                    'inherited' => [
                        'name' => 'inherited',
                        '@extends' => 'invalid'
                    ]
                ]
            ]
        ]);
        $container->setService(LoggerFactory::class, new LoggerFactory(
            $container,
            new HandlerPluginManager($container, []),
            new ProcessorPluginManager($container, []),
            new FormatterPluginManager($container, [])
        ));

        $factory = new LoggerAbstractFactory();

        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Offset invalid does not exist');
        $factory($container, 'inherited')->getName();

    }

    public function testFailsIfInheritedTargetIsNotString(): void {

        $container = new ServiceManager();
        $container->setService('Config', [
            'monolog' => [
                'loggers' => [
                    'inherited' => [
                        'name' => 'inherited',
                        '@extends' => []
                    ]
                ]
            ]
        ]);
        $container->setService(LoggerFactory::class, new LoggerFactory(
            $container,
            new HandlerPluginManager($container, []),
            new ProcessorPluginManager($container, []),
            new FormatterPluginManager($container, [])
        ));

        $factory = new LoggerAbstractFactory();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('@extends must be string');
        $factory($container, 'inherited')->getName();

    }

    public function testFailsIfInheritanceLevelsExceeded(): void {

        $container = new ServiceManager();
        $container->setService('Config', [
            'monolog' => [
                'loggers' => [
                    'base' => [
                        'name' => 'base'
                    ],
                    'inherited1' => [
                        '@extends' => 'base'
                    ],
                    'inherited2' => [
                        '@extends' => 'inherited1'
                    ],
                    'inherited3' => [
                        '@extends' => 'inherited2'
                    ],
                    'inherited4' => [
                        '@extends' => 'inherited3'
                    ],
                    'inherited5' => [
                        '@extends' => 'inherited4'
                    ],
                    'inherited6' => [
                        '@extends' => 'inherited5'
                    ],
                    'inherited7' => [
                        '@extends' => 'inherited6'
                    ],
                    'inherited8' => [
                        '@extends' => 'inherited7'
                    ],
                    'inherited9' => [
                        '@extends' => 'inherited8'
                    ],
                    'inherited10' => [
                        '@extends' => 'inherited9'
                    ],
                    'inherited11' => [
                        '@extends' => 'inherited10'
                    ],
                ]
            ]
        ]);
        $container->setService(LoggerFactory::class, new LoggerFactory(
            $container,
            new HandlerPluginManager($container, []),
            new ProcessorPluginManager($container, []),
            new FormatterPluginManager($container, [])
        ));

        $factory = new LoggerAbstractFactory();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Maximum inheritance level of 10 reached');
        $factory($container, 'inherited11')->getName();

    }

    public function testDefaultLevelIsApplied(): void {
        $container = new ServiceManager();
        $container->setService('Config', [
            'monolog' => [
                'defaults' => [
                    'level' => 123
                ],
                'loggers' => [
                    'base' => [
                        'name' => 'default',
                        'handlers' => [
                            'test' => [
                                'name' => StreamHandler::class,
                                'options' => [
                                    'stream' => STDOUT
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);
        $container->setService(LoggerFactory::class, new LoggerFactory(
            $container,
            new HandlerPluginManager($container, [
                'abstract_factories' => [
                    ReflectionAbstractFactory::class
                ]
            ]),
            new ProcessorPluginManager($container, []),
            new FormatterPluginManager($container, [])
        ));

        $factory = new LoggerAbstractFactory();

        self::assertEquals(123, $factory($container, 'base')->getHandlers()[0]->getLevel());

    }

    public function testDefaultLevelDoesNotOverrideConfiguredLevel(): void {
        $container = new ServiceManager();
        $container->setService('Config', [
            'monolog' => [
                'defaults' => [
                    'level' => 123
                ],
                'loggers' => [
                    'base' => [
                        'name' => 'default',
                        'handlers' => [
                            'test' => [
                                'name' => StreamHandler::class,
                                'options' => [
                                    'stream' => STDOUT,
                                    'level' => 100
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);
        $container->setService(LoggerFactory::class, new LoggerFactory(
            $container,
            new HandlerPluginManager($container, [
                'abstract_factories' => [
                    ReflectionAbstractFactory::class
                ]
            ]),
            new ProcessorPluginManager($container, []),
            new FormatterPluginManager($container, [])
        ));

        $factory = new LoggerAbstractFactory();

        self::assertEquals(100, $factory($container, 'base')->getHandlers()[0]->getLevel());

    }

    public function testDefaultProcessorIsApplied(): void {

        $container = new ServiceManager();
        $container->setService('Config', [
            'monolog' => [
                'defaults' => [
                    'processors' => [
                        'name' => ProcessIdProcessor::class
                    ]
                ],
                'loggers' => [
                    'base' => [
                        'name' => 'default',
                        'handlers' => [
                            'test' => [
                                'name' => StreamHandler::class,
                                'options' => [
                                    'stream' => STDOUT
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);
        $container->setService(LoggerFactory::class, new LoggerFactory(
            $container,
            new HandlerPluginManager($container, [
                'abstract_factories' => [
                    ReflectionAbstractFactory::class
                ]
            ]),
            new ProcessorPluginManager($container, [
                'abstract_factories' => [
                    ReflectionAbstractFactory::class
                ]
            ]),
            new FormatterPluginManager($container, [])
        ));

        $factory = new LoggerAbstractFactory();

        self::assertInstanceOf(ProcessIdProcessor::class, $factory($container, 'base')->popProcessor());

    }

    public function testDefaultFormatterIsApplied(): void {

        $container = new ServiceManager();
        $container->setService('Config', [
            'monolog' => [
                'defaults' => [
                    'formatter' => [
                        'name' => LineFormatter::class
                    ]
                ],
                'loggers' => [
                    'base' => [
                        'name' => 'default',
                        'handlers' => [
                            'test' => [
                                'name' => StreamHandler::class,
                                'options' => [
                                    'stream' => STDOUT
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);
        $container->setService(LoggerFactory::class, new LoggerFactory(
            $container,
            new HandlerPluginManager($container, [
                'abstract_factories' => [
                    ReflectionAbstractFactory::class
                ]
            ]),
            new ProcessorPluginManager($container, [
                'abstract_factories' => [
                    ReflectionAbstractFactory::class
                ]
            ]),
            new FormatterPluginManager($container, [
                'abstract_factories' => [
                    ReflectionAbstractFactory::class
                ]
            ])
        ));

        $factory = new LoggerAbstractFactory();

        self::assertInstanceOf(LineFormatter::class, $factory($container, 'base')->getHandlers()[0]->getFormatter());

    }

    public function testDefaultFormatterDoesNotOverrideConfiguredFormatter(): void {

        $container = new ServiceManager();
        $container->setService('Config', [
            'monolog' => [
                'defaults' => [
                    'formatter' => [
                        'name' => LineFormatter::class
                    ]
                ],
                'loggers' => [
                    'base' => [
                        'name' => 'default',
                        'handlers' => [
                            'test' => [
                                'name' => StreamHandler::class,
                                'options' => [
                                    'stream' => STDOUT
                                ],
                                'formatter' => [
                                    'name' => ChromePHPFormatter::class
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);
        $container->setService(LoggerFactory::class, new LoggerFactory(
            $container,
            new HandlerPluginManager($container, [
                'abstract_factories' => [
                    ReflectionAbstractFactory::class
                ]
            ]),
            new ProcessorPluginManager($container, [
                'abstract_factories' => [
                    ReflectionAbstractFactory::class
                ]
            ]),
            new FormatterPluginManager($container, [
                'abstract_factories' => [
                    ReflectionAbstractFactory::class
                ]
            ])
        ));

        $factory = new LoggerAbstractFactory();

        self::assertNotInstanceOf(LineFormatter::class, $factory($container, 'base')->getHandlers()[0]->getFormatter());

    }

}
