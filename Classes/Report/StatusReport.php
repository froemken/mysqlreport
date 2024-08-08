<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Report;

use StefanFroemken\Mysqlreport\Configuration\ExtConf;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Reports\Status;
use TYPO3\CMS\Reports\StatusProviderInterface;

/**
 * Provides a status report about mysqlreport
 */
readonly class StatusReport implements StatusProviderInterface
{
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
                $this->getAddExplainValue() ? ContextualFeedbackSeverity::WARNING : ContextualFeedbackSeverity::OK,
            ),
        ];
    }

    public function getLabel(): string
    {
        return 'EXT:mysqlreport';
    }

    private function getAddExplainValue(): bool
    {
        return $this->getExtConf()->isActivateExplainQuery();
    }

    private function getExtConf(): ExtConf
    {
        return GeneralUtility::makeInstance(ExtConf::class);
    }
}
