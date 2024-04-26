<?php

declare(strict_types=1);

use StefanFroemken\Mysqlreport\Backend\ToolbarItem\MySqlReportToolbarItem;
use StefanFroemken\Mysqlreport\Backend\ToolbarItem\MySqlReportToolbarItemV11;
use StefanFroemken\Mysqlreport\Controller\MySqlReportController;
use StefanFroemken\Mysqlreport\DependencyInjection;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;

return static function (ContainerConfigurator $container, ContainerBuilder $containerBuilder) {
    $containerBuilder->addCompilerPass(new DependencyInjection\DashboardPass('dashboard.widget'));

    $typo3Version = GeneralUtility::makeInstance(Typo3Version::class);
    $composerResource = new \Symfony\Component\Config\Resource\ComposerResource();
    foreach ($composerResource->getVendors() as $vendorPath) {
        $sqlFormatterDir = $vendorPath . '/doctrine/sql-formatter/src';
        if (is_dir($sqlFormatterDir)) {
            $container->services()->load('Doctrine\\SqlFormatter\\', $sqlFormatterDir);
        }
    }

    // Because of RequestAwareToolbarItemInterface is not known in TYPO3 11,
    // we have two different Toolbar classes which have to be handled differently
    $container->services()->load(
        'StefanFroemken\\Mysqlreport\\Backend\\ToolbarItem\\',
        '../Classes/Backend/ToolbarItem/*'
    );
    if (version_compare($typo3Version->getBranch(), '12.0', '>')) {
        $containerBuilder->removeDefinition(MySqlReportToolbarItemV11::class);

        $definition = $containerBuilder->findDefinition(MySqlReportToolbarItem::class);
        $definition->setAutoconfigured(true);
    } else {
        $containerBuilder->removeDefinition(MySqlReportToolbarItem::class);

        $definition = $containerBuilder->findDefinition(MySqlReportToolbarItemV11::class);
        $definition->setAutoconfigured(false);
    }
    $definition->setAutowired(true);
    $definition->setPublic(true);
    $definition->setShared(false);

    $containerBuilder->addCompilerPass(new class () implements CompilerPassInterface {
        /**
         * Lazy loading page services
         */
        public function process(ContainerBuilder $container): void
        {
            $mySqlReportControllerDefinition = $container->findDefinition(MySqlReportController::class);
            $mySqlReportControllerDefinition
                ->setPublic(true)
                ->setArgument('$serviceLocator', ServiceLocatorTagPass::register(
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
                ));
        }
    });
};
