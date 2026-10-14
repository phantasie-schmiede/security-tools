<?php
declare(strict_types=1);

/*
 * This file is part of PSBits Security.
 *
 * The copyright and license information can be found in the file LICENSE.txt,
 * which was distributed with this source code.
 */

namespace PSBits\Security\Domain\Model;

use PSBits\Foundation\Attribute\TCA\Column;
use PSBits\Foundation\Attribute\TCA\ColumnType\Input;
use PSBits\Security\Exception\CorruptedDataException;
use PSBits\Security\Utility\SecurityUtility;
use ReflectionException;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Security\Exception\InvalidArgumentForHashGenerationException;

/**
 * Class AbstractProtectedModel
 *
 * Let your domain models extend this class to protect their data from unwanted manipulation. Remember to call the
 * parent constructor in your model's constructor after initializing the properties, e.g. ObjectStorages!
 * If your repository extends the AbstractProtectedRepository, the checksum will be calculated and set automatically
 * when adding or updating a record of this model.
 *
 * Keep in mind that the checksum is calculated based on a canonical representation of the object (see
 * \PSBits\Security\Utility\SecurityUtility::canonicalize()) and the encryption key of your TYPO3 installation. System
 * metadata fields (pid, tstamp, crdate) are excluded, the record identity (uid) is included. If you change the
 * encryption key, add or remove properties, edit the record directly in the database or update the model in your code
 * without updating the checksum, the checksum will not match anymore and the model will throw an exception when trying
 * to access it! Provide UpgradeWizards when updating the database schema or the model to recalculate the checksum for
 * all existing records.
 *
 * @package PSBits\Security\Domain\Model
 */
abstract class AbstractProtectedModel extends AbstractEntity implements DataManipulationProtectionInterface
{
    #[Column(readOnly: true)]
    #[Input]
    protected string $checksum = '';

    /**
     * @throws InvalidArgumentForHashGenerationException
     * @throws ReflectionException
     */
    public function calculateChecksum(bool $store): string
    {
        $oldChecksum = $this->getChecksum();

        /*
         * The "checksum" property itself is excluded from the canonical representation, so it does not need to be
         * reset before calculating a new checksum.
         */
        $newChecksum = SecurityUtility::generateHash(SecurityUtility::canonicalize($this));

        if (true === $store) {
            $this->setChecksum($newChecksum);
        } else {
            $this->setChecksum($oldChecksum);
        }

        return $newChecksum;
    }

    public function getChecksum(): string
    {
        return $this->checksum;
    }

    public function setChecksum(string $checksum): void
    {
        $this->checksum = $checksum;
    }

    /**
     * @throws CorruptedDataException
     * @throws InvalidArgumentForHashGenerationException
     * @throws ReflectionException
     */
    public function validateChecksum(): void
    {
        if (!hash_equals($this->getChecksum(), $this->calculateChecksum(false))) {
            throw new CorruptedDataException(
                static::class . ': Checksum validation failed! The data of the record with UID ' . $this->getUid(
                ) . ' has been manipulated!', 1582819384
            );
        }
    }
}
