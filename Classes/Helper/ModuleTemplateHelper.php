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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Helper to add buttons to button bar
 */
class ModuleTemplateHelper
{
    private UriBuilder $uriBuilder;

    public function __construct(UriBuilder $uriBuilder)
    {
        $this->uriBuilder = $uriBuilder;
    }

    public function addOverviewButton(ButtonBar $buttonBar): void
    {
        $overviewButton = $buttonBar
            ->makeLinkButton()
            ->setShowLabelText(true)
            ->setTitle('Overview')
            ->setIcon($this->getIconFactory()->getIcon('actions-viewmode-tiles', IconSize::SMALL))
            ->setHref(
                (string)$this->uriBuilder->buildUriFromRoute('system_mysqlreport'),
            );

        $buttonBar->addButton($overviewButton, ButtonBar::BUTTON_POSITION_LEFT);
    }

    /**
     * @param ButtonBar $buttonBar
     * @param string $routeIdentifier
     * @param string $displayName
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

    protected function getIconFactory(): IconFactory
    {
        return GeneralUtility::makeInstance(IconFactory::class);
    }
}
