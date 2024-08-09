<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Domain\Model;

/**
 * Storage for MySQL|MariaDB variables
 * It is just a storage. So no traversable, countable, ...
 *
 * @implements \ArrayAccess<int|string|null, mixed>
 */
class Variables implements \ArrayAccess
{
    /**
     * @var array<int|string|null, mixed>
     */
    private array $storage;

    /**
     * @param array<int|string|null, mixed> $storage
     */
    public function __construct(array $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param int|string|null $offset
     */
    public function offsetSet($offset, mixed $value): void
    {
        if ($offset === null) {
            $this->storage[] = $value;
        } else {
            $this->storage[$offset] = $value;
        }
    }

    public function offsetExists($offset): bool
    {
        return isset($this->storage[$offset]);
    }

    public function offsetUnset($offset): void
    {
        unset($this->storage[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->storage[$offset] ?? null;
    }
}
