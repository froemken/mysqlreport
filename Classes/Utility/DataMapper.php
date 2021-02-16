<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Reflection\ReflectionService;

/**
 * Simple DataMapper to map an array to object
 */
class DataMapper
{
    /**
     * @var ReflectionService
     */
    protected $reflectionService;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    public function __construct(
        ReflectionService $reflectionService = null,
        ObjectManager $objectManager = null
    ) {
        $this->objectManager = $objectManager ?? GeneralUtility::makeInstance(ObjectManager::class);
        $this->reflectionService = $reflectionService ?? $this->objectManager->get(ReflectionService::class);
    }

    /**
     * Maps a single row on an object of the given class
     *
     * @param string $className The name of the target class
     * @param array $row A single array with field_name => value pairs
     * @return object An object of the given class
     */
    public function mapSingleRow($className, array $row)
    {
        if (class_exists($className)) {
            $object = $this->objectManager->get($className);
        } else {
            return null;
        }

        // loop through all properties
        foreach ($row as $propertyName => $value) {
            $propertyName = GeneralUtility::underscoredToLowerCamelCase($propertyName);
            $methodName = 'set' . ucfirst($propertyName);

            // if setter exists
            if (method_exists($object, $methodName)) {
                // get property type
                $propertyData = $this->reflectionService->getClassSchema($className)->getProperty($propertyName);
                switch ($propertyData['type']) {
                    case 'array':
                        $object->$methodName((array)$value);
                        break;
                    case 'int':
                    case 'integer':
                        $object->$methodName((int)$value);
                        break;
                    case 'bool':
                    case 'boolean':
                        $object->$methodName($value);
                        break;
                    case 'string':
                        $object->$methodName((string)$value);
                        break;
                    case 'float':
                        $object->$methodName((float)$value);
                        break;
                    case 'SplObjectStorage':
                    case 'Tx_Extbase_Persistence_ObjectStorage':
                    case ObjectStorage::class:
                        $object->$methodName($this->mapObjectStorage($propertyData['elementType'], $value));
                        break;
                    default:
                        if (class_exists($propertyData['type'])) {
                            $object->$methodName($this->mapSingleRow($propertyData['type'], $value));
                        }
                }
            }
        }
        return $object;
    }

    /**
     * map a object storage with given rows
     *
     * @param string $className
     * @param array $rows
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function mapObjectStorage($className, array $rows)
    {
        $objectStorage = $this->objectManager->get(ObjectStorage::class);
        foreach ($rows as $row) {
            $objectStorage->attach($this->mapSingleRow($className, $row));
        }
        return $objectStorage;
    }
}
