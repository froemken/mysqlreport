<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Service;

final readonly class ModuleAvailabilityService implements ModuleAvailabilityServiceInterface
{
    public function __construct(
        private DatabaseCapabilityServiceInterface $databaseCapabilityService,
    ) {}

    public function isModuleAvailable(string $moduleIdentifier): bool
    {
        return match ($moduleIdentifier) {
            'mysqlreport_querycache' => $this->databaseCapabilityService->isQueryCacheSupported(),
            default => true,
        };
    }
}
