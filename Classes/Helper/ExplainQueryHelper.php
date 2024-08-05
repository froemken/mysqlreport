<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Helper;

use StefanFroemken\Mysqlreport\Domain\Model\Profile;

/**
 * Helper to analyze EXPLAIN query result and add information to query profile model
 */
readonly class ExplainQueryHelper
{
    /**
     * @param Profile $profile
     * @param array<string, mixed> $explainRow
     * @return void
     */
    public function updateProfile(Profile $profile, array $explainRow): void
    {
        $profile->getExplainInformation()->addExplainResult($explainRow);
        if ($explainRow === []) {
            return;
        }

        if (empty($explainRow['key'])) {
            $profile->getExplainInformation()->setIsQueryUsingIndex(false);
        }

        if (strtolower($explainRow['type'] ?? '') === 'all') {
            $profile->getExplainInformation()->setIsQueryUsingFTS(true);
        }
    }
}
