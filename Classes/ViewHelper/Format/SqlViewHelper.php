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
 * ViewHelper to format a SQL statement using "doctrine/sql-formatter" when available.
 *
 * In Classic/ZIP installations where "doctrine/sql-formatter" might not be present,
 * the formatter dependency is optional and a sanitized fallback is rendered.
 */
final class SqlViewHelper extends AbstractViewHelper
{
    /**
     * SqlFormatter returns HTML markup, so output escaping must be disabled.
     */
    protected $escapeOutput = false;

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
            '',
        );
    }

    public function render(): string
    {
        $query = trim((string)($this->arguments['query'] ?? ''));
        if ($query === '' && ($this->renderChildrenClosure !== null || $this->viewHelperNode !== null)) {
            $query = trim((string)$this->renderChildren());
        }

        if ($query === '') {
            return '';
        }

        if (!$this->sqlFormatter instanceof SqlFormatter) {
            return htmlspecialchars($query, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return $this->sqlFormatter->format($query);
    }
}
