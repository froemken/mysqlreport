<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxInterface;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxService;
use StefanFroemken\Mysqlreport\Service\ModuleAvailabilityServiceInterface;
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Error\Http\UnauthorizedException;

#[AsController]
final readonly class QueryCacheController
{
    /**
     * @param iterable<int, InfoBoxInterface> $infoBoxes
     */
    public function __construct(
        private iterable $infoBoxes,
        private InfoBoxService $infoBoxService,
        private ModuleTemplateFactory $moduleTemplateFactory,
        private ModuleAvailabilityServiceInterface $moduleAvailabilityService,
    ) {}

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        if (!$this->moduleAvailabilityService->isModuleAvailable('mysqlreport_querycache')) {
            throw new UnauthorizedException('Query Cache is not supported on this database server.', 1774438001);
        }

        $moduleTemplate = $this->moduleTemplateFactory->create($request);
        $moduleTemplate->getDocHeaderComponent()->setShortcutContext(
            'mysqlreport_querycache',
            'MySQL Report - Query Cache',
        );

        $moduleTemplate->assign(
            'infoBoxes',
            $this->infoBoxService->buildInfoBoxes($this->infoBoxes),
        );

        return $moduleTemplate->renderResponse('QueryCache/Index');
    }
}
