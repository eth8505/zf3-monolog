<?php

namespace Eth8505Test\Monolog\Processor;

use Eth8505\Monolog\Processor\ProcessorPluginManager;
use Monolog\Processor\ProcessorInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

class ProcessorPluginManagerTest extends TestCase
{

    public function testGetPlugin(): void
    {

        $handler = $this->createMock(ProcessorInterface::class);

        $handlerPluginManager = new ProcessorPluginManager($this->createMock(ContainerInterface::class), [
            'services' => [
                ProcessorInterface::class => $handler,
            ],
        ]);
        $handlerReturned = $handlerPluginManager->get(ProcessorInterface::class);
        $this->assertSame($handler, $handlerReturned);

    }

    public function testValidateInvalidPlugin()
    {

        $this->expectException(ServiceNotFoundException::class);

        $handlerPluginManager = new ProcessorPluginManager($this->createMock(ContainerInterface::class));
        $handlerPluginManager->get('invalidpluginname');

    }

}
