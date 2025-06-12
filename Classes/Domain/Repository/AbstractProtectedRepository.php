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
     * @param object $object
     *
     * @throws IllegalObjectTypeException
     */
    public function add($object): void
    {
        if ($object instanceof DataManipulationProtectionInterface) {
            $object->calculateChecksum(true);
        }

        parent::add($object);
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
