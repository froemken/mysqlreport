<?php

declare(strict_types=1);

use Doctrine\SqlFormatter\SqlFormatter;
use StefanFroemken\Mysqlreport\Controller\MySqlReportController;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;

return static function (ContainerConfigurator $container, ContainerBuilder $containerBuilder) {
    if (
        class_exists(SqlFormatter::class)
        && $sqlFormatterReflection = $containerBuilder->getReflectionClass(SqlFormatter::class)
    ) {
        $containerBuilder->addResource(new FileResource($sqlFormatterReflection->getFileName()));
        $containerBuilder->register(SqlFormatter::class, SqlFormatter::class)->setPublic(true);
    }

    $containerBuilder
        ->registerForAutoconfiguration(\Symfony\Contracts\Service\ServiceSubscriberInterface::class)
        ->addTag('container.service_subscriber');

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
