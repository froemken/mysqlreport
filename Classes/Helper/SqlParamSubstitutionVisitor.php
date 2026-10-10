<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Helper;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\SQL\Parser\Visitor;
use Doctrine\DBAL\Types\Type;

/**
 * Visitor that substitutes SQL placeholders with converted parameter values.
 *
 * @internal
 */
final class SqlParamSubstitutionVisitor implements Visitor
{
    private int $positionalIndex;

    /**
     * @var list<string>
     */
    private array $sqlFragments = [];

    /**
     * @param array<int|string, mixed> $params
     * @param array<int|string, int|string> $types
     */
    public function __construct(
        private readonly array $params,
        private readonly array $types,
        private readonly AbstractPlatform $platform,
    ) {
        $this->positionalIndex = array_key_exists(0, $this->params) ? 0 : 1;
    }

    public function acceptPositionalParameter(string $sql): void
    {
        $key = $this->positionalIndex++;
        $this->sqlFragments[] = $this->resolveParamValue($key, $sql);
    }

    public function acceptNamedParameter(string $sql): void
    {
        $name = substr($sql, 1);
        $key = array_key_exists($name, $this->params) ? $name : $sql;
        $this->sqlFragments[] = $this->resolveParamValue($key, $sql);
    }

    public function acceptOther(string $sql): void
    {
        $this->sqlFragments[] = $sql;
    }

    public function getSql(): string
    {
        return implode('', $this->sqlFragments);
    }

    private function resolveParamValue(int|string $key, string $fallback): string
    {
        if (!array_key_exists($key, $this->params)) {
            return $fallback;
        }

        $param = $this->params[$key];
        $typeIdentifier = $this->types[$key] ?? null;
        $type = is_string($typeIdentifier) && Type::hasType($typeIdentifier)
            ? Type::getType($typeIdentifier)
            : null;
        $value = $type?->convertToDatabaseValue($param, $this->platform) ?? $param;

        return var_export($value, true);
    }
}
