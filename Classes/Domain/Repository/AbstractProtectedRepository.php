<?php
declare(strict_types=1);

/*
 * This file is part of PSBits Security.
 *
 * The copyright and license information can be found in the file LICENSE.txt,
 * which was distributed with this source code.
 */

namespace PSBits\Security\Domain\Repository;

use PSBits\Security\Domain\Model\DataManipulationProtectionInterface;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Class AbstractProtectedRepository
 *
 * @package PSBits\Security\Domain\Repository
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

        if ($object instanceof DataManipulationProtectionInterface) {
            /*
             * First store the object to the persistence layer so that all properties like UID are available and
             * then update the object with the calculated checksum.
             */
            $this->persistenceManager->persistAll();
            $this->update($object);
        }
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
