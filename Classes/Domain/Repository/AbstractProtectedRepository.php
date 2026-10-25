<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Security Tools.
 *
 * The copyright and license information can be found in the file LICENSE.txt,
 * which was distributed with this source code.
 */

namespace PSBits\SecurityTools\Domain\Repository;

use PSBits\SecurityTools\Domain\Model\DataManipulationProtectionInterface;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Class AbstractProtectedRepository
 *
 * @template T of AbstractEntity
 * @extends Repository<T>
 * @package PSBits\SecurityTools\Domain\Repository
 */
abstract class AbstractProtectedRepository extends Repository
{
    /**
     * @param AbstractEntity $object
     *
     * @return void
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     */
    public function add($object): void
    {
        parent::add($object);

        if (!$object instanceof DataManipulationProtectionInterface) {
            return;
        }

        /*
         * The checksum is bound to the record identity (uid), which is only known after the object has been persisted
         * for the first time. So the object is stored once to obtain the uid, the checksum is calculated and the object
         * is stored again, so that the checksum is reliably persisted even if no further persistAll() is triggered.
         */
        $this->persistenceManager->persistAll();
        $this->update($object);
        $this->persistenceManager->persistAll();
    }

    /**
     * @param object $modifiedObject
     *
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     */
    public function update($modifiedObject): void
    {
        if ($modifiedObject instanceof DataManipulationProtectionInterface) {
            $modifiedObject->calculateChecksum(true);
        }

        parent::update($modifiedObject);
    }
}
