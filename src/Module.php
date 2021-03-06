<?php declare(strict_types=1);

namespace Eth8505\Monolog;

use Zend\ModuleManager\Feature\ConfigProviderInterface;

/**
 * Monolog integration module
 */
class Module implements ConfigProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

}