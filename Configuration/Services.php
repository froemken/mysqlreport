<?php

declare(strict_types=1);

use StefanFroemken\Mysqlreport\Controller\MySqlReportController;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;

return static function (ContainerConfigurator $container, ContainerBuilder $containerBuilder) {
    $containerBuilder->addCompilerPass(new class () implements CompilerPassInterface {
        /**
         * Lazy loading page services
         */
        public function process(ContainerBuilder $container): void
        {
            $mySqlReportControllerDefinition = $container->findDefinition(MySqlReportController::class);
            $mySqlReportControllerDefinition->addMethodCall('injectServiceLocator', [
                ServiceLocatorTagPass::register(
                    $container,
                    [
                        'page.information' => new Reference('mysqlreport.page.information'),
                        'page.innodb' => new Reference('mysqlreport.page.innodb'),
                        'page.misc' => new Reference('mysqlreport.page.misc'),
                        'page.query_cache' => new Reference('mysqlreport.page.query_cache'),
                        'page.table_cache' => new Reference('mysqlreport.page.table_cache'),
                        'page.thread_cache' => new Reference('mysqlreport.page.thread_cache'),
                        'repository.status' => new Reference('mysqlreport.repository.status'),
                        'repository.variables' => new Reference('mysqlreport.repository.variables'),
                    ]
                )
            ]);
        }
    });
};
