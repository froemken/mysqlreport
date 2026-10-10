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
use StefanFroemken\Mysqlreport\InfoBox\RenderInfoBoxFactory;
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;

#[AsController]
final readonly class MiscController
{
    /**
     * @param iterable<int, InfoBoxInterface> $infoBoxes
     */
    public function __construct(
        private iterable $infoBoxes,
        private RenderInfoBoxFactory $renderInfoBoxFactory,
        private ModuleTemplateFactory $moduleTemplateFactory,
    ) {}

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($request);
        $moduleTemplate->getDocHeaderComponent()->setShortcutContext(
            'mysqlreport_misc',
            'MySQL Report - Misc',
        );

        $moduleTemplate->assign(
            'renderedInfoBoxes',
            $this->renderInfoBoxFactory->render($this->infoBoxes),
        );

        return $moduleTemplate->renderResponse('Misc/Index');
    }
}
