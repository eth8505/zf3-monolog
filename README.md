# Eth8505\Monolog - ZF3 module integrating monolog with zend framework v3
The **Eth8505\Monolog** module integrates [monolog/monolog](https://github.com/seldaek/monolog) as a zf3-module 
via [zendframework/zend-servicemanager](https://github.com/zendframework/zend-servicemanager).

## How to install

:warning: Please note that this package requires at least php 7.1.  
Install `eth8505/zf3-monolog` package via composer.

~~~bash
$ composer require eth8505/zf3-monolog
~~~

Load the module in your `application.config.php` file like so:

~~~php
<?php

return [
	'modules' => [
		'Eth8505\\Monolog',
		// ...
	],
];
~~~

## How to use
In your application config (usually located in `config/autoload/monolog.global.php`), specify your monolog in the 
`monolog/loggers` key.

### Configuring loggers
Each key (```Log\MyApp``` in the sample code) can contain a separate logger config and is available directly via the
service manager. 

~~~php
return [
    'monolog' => [
        'loggers' => [
            'Log\MyApp' => [
                'name' => 'default'
            ]
        ]
    ]
];
~~~

Each logger config is available direcly via the service manager.
~~~php
$logger = $container->get('Log\MyApp');
~~~

### Adding log handlers
Multiple [handlers](https://github.com/Seldaek/monolog/blob/master/doc/02-handlers-formatters-processors.md#handlers) 
can be added to a logger config via the ```handlers``` key.
~~~php
return [
    'monolog' => [
        'loggers' => [
            'Log\MyApp' => [
                'name' => 'default',
                'handlers' => [
                    'stream' => [
                        'name' => StreamHandler::class,
                        'options' => [
                            'path'   => 'data/log/myapp.log',
                            'level'  => Logger::DEBUG
                        ],
                    ],
                    'fire_php' => [
                        'name' => ChromePHPHandler:class
                    ]
                ]
            ]
        ]
    ]
];
~~~

### Using formatters
Each handler can be configured with a [formatter](https://github.com/Seldaek/monolog/blob/master/doc/02-handlers-formatters-processors.md#formatters) 
in order to specify a specific format. This can be useful whenlogging to [logstash](https://www.elastic.co/de/products/logstash) 
for example.

~~~php
return [
    'monolog' => [
        'loggers' => [
            'Log\MyApp' => [
                'name' => 'default',
                'handlers' => [
                    'stream' => [
                        'name' => StreamHandler::class,
                        'options' => [
                            'path'   => 'data/log/myapp.log',
                            'level'  => Logger::DEBUG
                        ],
                        'formatter' => [
                            'name' => LogstashFormatter::class,
                            'options' => [
                                'applicationName' => 'myApp',
                                'systemName' => gethostname()
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];
~~~

### Using processors
[Processors](https://github.com/Seldaek/monolog/blob/master/doc/02-handlers-formatters-processors.md#processors) can be
used to enrich the logged data with additional data. The [WebProcessor](https://github.com/Seldaek/monolog/blob/master/src/Monolog/Processor/WebProcessor.php)
can for example be used to add the request URI and client IP to the log record.
~~~php
return [
    'monolog' => [
        'loggers' => [
            'Log\MyApp' => [
                'name' => 'default'
                'processors' => [
                    WebProcessor::class
                ]
            ]
        ]
    ]
];
~~~

### Special syntax
When configuring handlers, formatters or processors, you can either specify a class name in string (or ::class constant)
format
~~~php
return [
    'monolog' => [
        'loggers' => [
            'Log\MyApp' => [
                'name' => 'default'
                'processors' => [
                    WebProcessor::class
                ]
            ]
        ]
    ]
];
~~~

or alternatively in name/options array notation, where the options are translated into the respective classes
constructor parameters by using [Reflection](https://php.net/Reflection) based 
[Named parameters](https://en.wikipedia.org/wiki/Named_parameter). 
~~~php
return [
    'monolog' => [
        'loggers' => [
            'Log\MyApp' => [
                'name' => 'default'
                'processors' => [
                    [
                        'name' => WebProcessor::class,
                        'options' => [
                            'extraFields' => [
                                'url' => 'REQUEST_URI',
                                'http_method' => 'REQUEST_METHOD',
                                'server' => 'SERVER_NAME'
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];
~~~

### Custom handlers, processors and formatters
Since this module creates everything via the service manager using 
[plugin managers](https://docs.zendframework.com/zend-servicemanager/plugin-managers/), custom handlers, 
processors and formatters can be easily registered, by adding them to the respective config keys

~~~php
return [
    'monolog' => [
        'formatters' => [
            MyCustomFormatter::class => MyCustomFormatterFactory::class
        ],
        'handlers' => [
            MyCustomHandler::class => MyCustomHandlerFactory::class
        ],
        'processors' => [
            MyCustomProcessor::class => MyCustomProcessorFactory::class
        ]
    ]
];
~~~
:warning: Note that only formatters using custom factories need to be ecplicitly registered. Any other handler
configured will be automatically created using the internal, reflection-based factories.

## Thanks
Thanks to [neckeloo](https://github.com/neeckeloo) and his [Monolog Module](https://github.com/neeckeloo/monolog-module)
and [enlitepro](https://github.com/enlitepro) for their [Enlite Monolog](https://github.com/enlitepro/enlite-monolog)
as they served as a template for this module.