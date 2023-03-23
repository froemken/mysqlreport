<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Dashboard\Widget;

use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

class ConnectionWidget implements WidgetInterface
{
    private WidgetConfigurationInterface $configuration;

    private StandaloneView $view;

    private array $options = [];

    public function __construct(
        WidgetConfigurationInterface $configuration,
        StandaloneView $view,
        array $options
    ) {
        $this->configuration = $configuration;
        $this->view = $view;
        $this->options = $options;
    }

    public function renderWidgetContent(): string
    {
        $this->view->setTemplate('Widget/Connections');
        $this->view->assignMultiple([
            'items' => [],
            'configuration' => $this->configuration,
            'options' => $this->getOptions(),
        ]);

        return $this->view->render();
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
