<?php

declare(strict_types=1);

use StefanFroemken\Mysqlreport\DependencyInjection;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container, ContainerBuilder $containerBuilder) {
    $containerBuilder->addCompilerPass(new DependencyInjection\DashboardPass('dashboard.widget'));

    $composerResource = new \Symfony\Component\Config\Resource\ComposerResource();
    foreach ($composerResource->getVendors() as $vendorPath) {
        $sqlFormatterDir = $vendorPath . '/doctrine/sql-formatter/src';
        if (is_dir($sqlFormatterDir)) {
            $container->services()->load('Doctrine\\SqlFormatter\\', $sqlFormatterDir);
        }
    }
};
