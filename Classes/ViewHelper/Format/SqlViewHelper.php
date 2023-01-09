<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\ViewHelper\Format;

use Doctrine\SqlFormatter\SqlFormatter;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to format a SQL statement, as far as "doctrine/sql-formatter" is available
 */
class SqlViewHelper extends AbstractViewHelper
{
    private ?SqlFormatter $sqlFormatter = null;

    public function setSqlFormatter(SqlFormatter $sqlFormatter): void
    {
        $this->sqlFormatter = $sqlFormatter;
    }

    public function initializeArguments(): void
    {
        $this->registerArgument(
            'query',
            'string',
            'The SQL statement (query) to format.',
            false,
            ''
        );
    }

    public function render(): string
    {
        $query = trim($this->arguments['query'] ?? '');
        if ($query === '') {
            $query = $this->renderChildren();
        }

        if ($query === '') {
            return '';
        }

        if (!$this->sqlFormatter instanceof SqlFormatter) {
            return $query;
        }

        return $this->sqlFormatter->format($query);
    }
}
