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
 */
class Variables implements \ArrayAccess
{
    private array $storage;

    public function __construct(array $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param int|string|null $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
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

    /**
     * @param mixed $offset
     * @return mixed|null
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->storage[$offset] ?? null;
    }
}
