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
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to format a SQL statement, as far as "doctrine/sql-formatter" is available
 */
class SqlViewHelper extends AbstractViewHelper implements ServiceSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $locator;

    public function __construct(ContainerInterface $locator)
    {
        $this->locator = $locator;
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

    public static function getSubscribedServices(): array
    {
        return [
            'sql_formatter' => '?' . SqlFormatter::class
        ];
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

        if (!$this->locator->has('sql_formatter')) {
            return $query;
        }

        return $this->getSqlFormatter()->format($query);
    }

    private function getSqlFormatter(): SqlFormatter
    {
        return $this->locator->get('sql_formatter');
    }
}
