<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Report;

use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Reports\Status;
use TYPO3\CMS\Reports\StatusProviderInterface;

/**
 * Provides a status report about mysqlreport
 */
class StatusReport implements StatusProviderInterface
{
    private ExtensionConfiguration $extensionConfiguration;

    public function __construct(ExtensionConfiguration $extensionConfiguration)
    {
        $this->extensionConfiguration = $extensionConfiguration;
    }

    /**
     * @return Status[]
     */
    public function getStatus(): array
    {
        if (Environment::isCli()) {
            return [];
        }

        return [
            'addExplain' => GeneralUtility::makeInstance(
                Status::class,
                'Add EXPLAIN',
                $this->getAddExplainValue() ? 'Active' : 'Deactivated',
                'If active, it slows down your system. Further it may break queries which relates to insert_id and affected_rows',
                $this->getAddExplainValue() ? ContextualFeedbackSeverity::WARNING : ContextualFeedbackSeverity::OK
            ),
        ];
    }

    public function getLabel(): string
    {
        return 'EXT:mysqlreport';
    }

    private function getAddExplainValue(): bool
    {
        $addExplain = false;
        try {
            $addExplain = (bool)$this->extensionConfiguration->get('mysqlreport', 'addExplain');
        } catch (ExtensionConfigurationExtensionNotConfiguredException | ExtensionConfigurationPathDoesNotExistException $e) {
        }

        return $addExplain;
    }
}
