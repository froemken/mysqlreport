<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\EventListener;

use StefanFroemken\Mysqlreport\Service\ModuleAvailabilityServiceInterface;
use TYPO3\CMS\Backend\Module\BeforeModuleCreationEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;

/**
 * Dynamically hides modules and denies access when required database features are unsupported.
 */
#[AsEventListener(identifier: 'mysqlreport-before-module-creation')]
final readonly class BeforeModuleCreationEventListener
{
    public function __construct(
        private ModuleAvailabilityServiceInterface $moduleAvailabilityService,
    ) {}

    public function __invoke(BeforeModuleCreationEvent $event): void
    {
        $identifier = $event->getIdentifier();
        if (!$this->moduleAvailabilityService->isModuleAvailable($identifier)) {
            $event->setConfigurationValue('appearance', ['renderInModuleMenu' => false]);
            $event->setConfigurationValue('access', 'mysqlreport_unavailable');
        }
    }
}
