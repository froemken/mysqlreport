<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Helper;

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Imaging\IconSize;

/**
 * Helper to add buttons to the button bar
 */
readonly class ModuleTemplateHelper
{
    public function __construct(
        private IconFactory $iconFactory,
        private UriBuilder $uriBuilder,
    ) {}

    public function addOverviewButton(ButtonBar $buttonBar): void
    {
        $overviewButton = $buttonBar
            ->makeLinkButton()
            ->setShowLabelText(true)
            ->setTitle('Overview')
            ->setIcon($this->iconFactory->getIcon('actions-viewmode-tiles', IconSize::SMALL))
            ->setHref(
                (string)$this->uriBuilder->buildUriFromRoute('system_mysqlreport'),
            );

        $buttonBar->addButton($overviewButton, ButtonBar::BUTTON_POSITION_LEFT);
    }

    /**
     * @param array<string, string> $arguments
     */
    public function addShortcutButton(
        ButtonBar $buttonBar,
        string $routeIdentifier,
        string $displayName,
        array $arguments = [],
    ): void {
        $shortcutButton = $buttonBar->makeShortcutButton()
            ->setRouteIdentifier($routeIdentifier)
            ->setDisplayName($displayName);

        if ($arguments !== []) {
            $shortcutButton->setArguments($arguments);
        }

        $buttonBar->addButton($shortcutButton, ButtonBar::BUTTON_POSITION_RIGHT);
    }
}
