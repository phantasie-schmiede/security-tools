<?php
declare(strict_types=1);

/*
 * This file is part of PSBits Security.
 *
 * The copyright and license information can be found in the file LICENSE.txt,
 * which was distributed with this source code.
 */

namespace PSBits\Security\Domain\Model;

use DateMalformedStringException;
use PSBits\Foundation\Attribute\TCA\ColumnType\Datetime;
use PSBits\Foundation\Attribute\TCA\ColumnType\Input;
use PSBits\Security\Utility\SecurityUtility;
use Random\RandomException;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Class AbstractToken
 *
 * @package PSBits\Security\Domain\Model
 */
abstract class AbstractToken extends AbstractEntity
{
    #[Datetime]
    protected \DateTime $expiresAt;

    // This property is used to store the raw token before hashing, not persisted in DB!
    protected string $rawToken;

    #[Input]
    protected string $selector;

    // Hashed token stored in DB
    #[Input]
    protected string $validator;

    /**
     * @throws DateMalformedStringException
     * @throws RandomException
     */
    public function __construct(string $lifetime)
    {
        $this->expiresAt = new \DateTime('+' . $lifetime);
        $this->rawToken = bin2hex(random_bytes(32));
        $this->selector = bin2hex(random_bytes(16));
        $this->validator = SecurityUtility::generatePasswordHash($this->rawToken);
    }

    public function getExpiresAt(): \DateTime
    {
        return $this->expiresAt;
    }

    public function getRawToken(): string
    {
        return $this->rawToken;
    }

    public function getSelector(): string
    {
        return $this->selector;
    }

    public function getValidator(): string
    {
        return $this->validator;
    }

    public function validate(string $token): bool
    {
        if (new \DateTime() > $this->expiresAt) {
            return false;
        }

        return SecurityUtility::verifyPassword($this->validator, $token);
    }
}
