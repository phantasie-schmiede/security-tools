<?php
declare(strict_types=1);

/*
 * This file is part of PSBits Security.
 *
 * The copyright and license information can be found in the file LICENSE.txt,
 * which was distributed with this source code.
 */

namespace PSBits\Security\Utility;

use TYPO3\CMS\Extbase\Security\Exception\InvalidArgumentForHashGenerationException;

/**
 * Class SecurityUtility
 *
 * The class \TYPO3\CMS\Extbase\Security\Cryptography\HashService offers almost the same functionality, but is marked as
 * internal and uses the rather vulnerable sha1 algorithm.
 *
 * @package PSBits\Security\Utility
 */
class SecurityUtility
{
    /**
     * @throws InvalidArgumentForHashGenerationException
     */
    public static function generateHash(string $string, string $algorithm = 'sha256'): string
    {
        return hash_hmac($algorithm, $string, self::getEncryptionKey());
    }

    public static function generatePasswordHash(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * @throws InvalidArgumentForHashGenerationException
     */
    public static function getEncryptionKey(): string
    {
        $encryptionKey = (string)$GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'];

        if (!$encryptionKey) {
            throw new InvalidArgumentForHashGenerationException(
                __CLASS__ . ': There is no encryption key defined! Go to "Settings->Configure Installation-Wide Options" to set one.',
                1582894686
            );
        }

        return $encryptionKey;
    }

    public static function verifyPassword(string $hash, string $password): bool
    {
        return password_verify($password, $hash);
    }
}
