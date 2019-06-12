<?php

namespace Eth8505Test\Monolog\Handler;

use Eth8505\Monolog\Handler\HandlerPluginManager;
use Monolog\Handler\HandlerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

class HandlerPluginManagerTest extends TestCase
{

    public function testGetPlugin(): void {

        $handler = $this->createMock(HandlerInterface::class);

        $handlerPluginManager = new HandlerPluginManager($this->createMock(ContainerInterface::class), [
            'services' => [
                HandlerInterface::class => $handler,
            ],
        ]);
        $handlerReturned = $handlerPluginManager->get(HandlerInterface::class);
        $this->assertSame($handler, $handlerReturned);

    }

    public function testValidateInvalidPlugin()
    {

        $this->expectException(ServiceNotFoundException::class);

        $handlerPluginManager = new HandlerPluginManager($this->createMock(ContainerInterface::class));
        $handlerPluginManager->get('invalidpluginname');

    }

}
