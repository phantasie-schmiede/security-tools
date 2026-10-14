<?php
declare(strict_types=1);

/*
 * This file is part of PSBits Security.
 *
 * The copyright and license information can be found in the file LICENSE.txt,
 * which was distributed with this source code.
 */

namespace PSBits\Security\Utility;

use DateTimeInterface;
use DateTimeZone;
use ReflectionClass;
use ReflectionException;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy;
use TYPO3\CMS\Extbase\Security\Exception\InvalidArgumentForHashGenerationException;
use UnitEnum;
use function gettype;
use function in_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_object;
use function is_string;
use const SORT_STRING;

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
     * System metadata fields excluded from the checksum. The checksum stays bound to the record identity (uid) but must
     * not depend on fields that change for legitimate reasons, e.g. page moves or save timestamps.
     */
    protected const array EXCLUDED_CHECKSUM_PROPERTIES = [
        'pid',
        'tstamp',
        'crdate',
        'checksum',
    ];

    /**
     * Computes a deterministic, canonical string representation of the given object that is used as input for the
     * checksum.
     *
     * Compared to a plain serialization this representation
     * - explicitly includes null values, so clearing a protected field is detectable,
     * - normalizes DateTime values to a fixed UTC format,
     * - represents related domain objects by their "ClassName:uid" instead of a raw, hydration dependent serialization,
     * - and is independent of the property declaration order.
     *
     * @throws ReflectionException
     */
    public static function canonicalize(object $object): string
    {
        $properties = self::canonicalizeProperties($object, true);
        ksort($properties);

        return implode(
            "\n",
            array_map(
                static fn(string $propertyName, string $value): string => $propertyName . '=' . $value,
                array_keys($properties),
                $properties,
            ),
        );
    }

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

    protected static function canonicalizeCollection(iterable $collection): string
    {
        $items = [];

        foreach ($collection as $item) {
            $items[] = self::canonicalizeValue($item);
        }

        sort($items, SORT_STRING);

        return '[' . implode(',', $items) . ']';
    }

    protected static function canonicalizeDateTime(DateTimeInterface $dateTime): string
    {
        $utc = clone $dateTime;
        $utc->setTimezone(new DateTimeZone('UTC'));

        return $utc->format('Y-m-d H:i:s');
    }

    /**
     * @return array<non-empty-string, string>
     */
    protected static function canonicalizeProperties(object $object, bool $topLevel): array
    {
        $properties = [];
        $reflectionClass = new ReflectionClass($object);

        foreach ($reflectionClass->getProperties() as $property) {
            $propertyName = $property->getName();

            if (str_starts_with($propertyName, '_')) {
                continue;
            }

            if ($topLevel && in_array($propertyName, self::EXCLUDED_CHECKSUM_PROPERTIES, true)) {
                continue;
            }

            $properties[$propertyName] = self::canonicalizeValue($property->getValue($object));
        }

        return $properties;
    }

    protected static function canonicalizeValue(mixed $value): string
    {
        if (null === $value) {
            return 'n';
        }

        if (is_bool($value)) {
            return 'b' . ($value ? '1' : '0');
        }

        if (is_int($value)) {
            return 'i' . $value;
        }

        if (is_float($value)) {
            return 'f' . $value;
        }

        if (is_string($value)) {
            return 's' . $value;
        }

        if ($value instanceof DateTimeInterface) {
            return 'd' . self::canonicalizeDateTime($value);
        }

        if ($value instanceof UnitEnum) {
            return 'e' . $value::class . ':' . $value->name;
        }

        if ($value instanceof LazyLoadingProxy) {
            return 'r' . $value->_getTypeAndUidString();
        }

        if ($value instanceof AbstractDomainObject) {
            return 'r' . $value::class . ':' . $value->getUid();
        }

        if (is_iterable($value)) {
            return 'c' . self::canonicalizeCollection($value);
        }

        if (is_object($value)) {
            $properties = self::canonicalizeProperties($value, false);
            ksort($properties);

            return 'o' . $value::class . '(' . implode(
                    ';',
                    array_map(
                        static fn(
                            string $propertyName,
                            string $propertyValue,
                        ): string => $propertyName . ':' . $propertyValue,
                        array_keys($properties),
                        $properties,
                    ),
                ) . ')';
        }

        return gettype($value);
    }
}
