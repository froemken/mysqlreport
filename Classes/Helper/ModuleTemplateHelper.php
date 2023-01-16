<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Helper;

use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

/**
 * Helper to add buttons to button bar
 */
class ModuleTemplateHelper
{
    private $uriBuilder;

    public function __construct(UriBuilder $uriBuilder)
    {
        $this->uriBuilder = $uriBuilder;
    }

    public function setRequest(Request $request): void
    {
        $this->uriBuilder->setRequest($request);
    }

    public function addOverviewButton(ButtonBar $buttonBar): void
    {
        $overviewButton = $buttonBar
            ->makeLinkButton()
            ->setShowLabelText(true)
            ->setTitle('Overview')
            ->setIcon($this->getIconFactory()->getIcon('actions-viewmode-tiles', Icon::SIZE_SMALL))
            ->setHref(
                $this->uriBuilder->uriFor(
                    'overview',
                    null,
                    'MySqlReport'
                )
            );
        $buttonBar->addButton($overviewButton, ButtonBar::BUTTON_POSITION_LEFT);
    }

    public function addShortcutButton(
        ButtonBar $buttonBar
    ): void {
        $shortcutButton = $buttonBar->makeShortcutButton()
            ->setModuleName('system_MysqlreportMysql')
            ->setGetVariables(['route', 'module', 'id'])
            ->setDisplayName('Shortcut');
        $buttonBar->addButton($shortcutButton, ButtonBar::BUTTON_POSITION_RIGHT);
    }

    protected function getIconFactory(): IconFactory
    {
        return GeneralUtility::makeInstance(IconFactory::class);
    }
}
