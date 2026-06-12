<?php

declare(strict_types=1);

use StefanFroemken\Mysqlreport\DependencyInjection\DashboardPass;
use Symfony\Component\Config\Resource\ComposerResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container, ContainerBuilder $containerBuilder): void {
    $containerBuilder->addCompilerPass(new DashboardPass('dashboard.widget'));

    $composerResource = new ComposerResource();
    foreach ($composerResource->getVendors() as $vendorPath) {
        $sqlFormatterDir = $vendorPath . '/doctrine/sql-formatter/src';
        if (is_dir($sqlFormatterDir)) {
            $container->services()->load('Doctrine\\SqlFormatter\\', $sqlFormatterDir);
        }
    }
};
