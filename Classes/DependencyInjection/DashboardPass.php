<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use TYPO3\CMS\Dashboard\Controller\DashboardController;

/**
 * EXT:mysqlreport should also be installable, if EXT:dashboard is not present in the system
 * As there is no possibility in Services.yaml to check for activated/installed extensions
 * I have moved that check into this CompilerPass.
 */
class DashboardPass implements CompilerPassInterface
{
    private string $tagName;

    public function __construct(string $tagName)
    {
        $this->tagName = $tagName;
    }

    /**
     * Start removing registered dashboard widgets, if EXT:dashboard is not installed
     *
     * This CompilerPass was called at a very early state, where $container was not injected into
     * GeneralUtility for makeInstance usage and ExtensionManagementUtility could not be used
     * as PackageManager was not injected into that class already. We also can not use the container itself, as the
     * real PackageManger will be added to $container AFTER processing all CompilerPasses.
     *
     * The only solution is, to check if there is a class definition of EXT:dashboard registered in given $container
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(DashboardController::class)) {
            foreach ($container->findTaggedServiceIds($this->tagName) as $id => $tags) {
                $container->removeDefinition($id);
            }
        }
    }
}
