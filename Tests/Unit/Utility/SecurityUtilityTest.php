<?php
declare(strict_types=1);

/*
 * This file is part of PSBits Security.
 *
 * The copyright and license information can be found in the file LICENSE.txt,
 * which was distributed with this source code.
 */

namespace PSBits\Security\Tests\Unit\Utility;

use DateMalformedStringException;
use DateTime;
use DateTimeZone;
use PSBits\Security\Tests\Unit\Fixtures\CanonicalizeSample;
use PSBits\Security\Tests\Unit\Fixtures\OrderSampleA;
use PSBits\Security\Tests\Unit\Fixtures\OrderSampleB;
use PSBits\Security\Tests\Unit\Fixtures\ProtectedSample;
use PSBits\Security\Tests\Unit\Fixtures\RelatedSample;
use PSBits\Security\Utility\SecurityUtility;
use ReflectionException;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class SecurityUtilityTest
 *
 * @package PSBits\Security\Tests\Unit\Utility
 */
class SecurityUtilityTest extends UnitTestCase
{
    public function testCanonicalizeIncludesNullValues(): void
    {
        $nullSample = SecurityUtility::canonicalize(new CanonicalizeSample(nullable: null));
        $filledSample = SecurityUtility::canonicalize(new CanonicalizeSample(nullable: 'x'));

        self::assertStringContainsString('nullable=n', $nullSample);
        self::assertStringContainsString('nullable=sx', $filledSample);
        self::assertNotSame($nullSample, $filledSample);
    }

    /**
     * @throws ReflectionException
     */
    public function testCanonicalizeIncludesUidButExcludesSystemFields(): void
    {
        $sample = new ProtectedSample();
        $sample->_setProperty('uid', 7);
        $sample->_setProperty('pid', 42);
        $sample->setName('foo');

        $canonical = SecurityUtility::canonicalize($sample);

        self::assertStringContainsString('uid=i7', $canonical);
        self::assertStringContainsString('name=sfoo', $canonical);
        self::assertStringNotContainsString('pid=', $canonical);
    }

    /**
     * @throws ReflectionException
     */
    public function testCanonicalizeIsIndependentOfPropertyDeclarationOrder(): void
    {
        self::assertSame(
            SecurityUtility::canonicalize(new OrderSampleA()),
            SecurityUtility::canonicalize(new OrderSampleB()),
        );
    }

    /**
     * @throws DateMalformedStringException
     * @throws ReflectionException
     */
    public function testCanonicalizeNormalizesDateTimeToUtc(): void
    {
        $utcSample = new CanonicalizeSample(
            dateTime: new DateTime('2020-01-01 00:00:00', new DateTimeZone('UTC')),
        );
        $berlinSample = new CanonicalizeSample(
            dateTime: new DateTime('2020-01-01 01:00:00', new DateTimeZone('Europe/Berlin')),
        );

        // 01:00 Europe/Berlin is 00:00 UTC, so both samples must canonicalize identically.
        self::assertSame(SecurityUtility::canonicalize($utcSample), SecurityUtility::canonicalize($berlinSample));
        self::assertStringContainsString('dateTime=d2020-01-01 00:00:00', SecurityUtility::canonicalize($utcSample));
    }

    public function testCanonicalizeRepresentsRelationByClassNameAndUid(): void
    {
        $related = new RelatedSample();
        $related->_setProperty('uid', 99);

        $sample = new ProtectedSample();
        $sample->_setProperty('uid', 7);
        $sample->setRelated($related);

        $canonical = SecurityUtility::canonicalize($sample);

        self::assertStringContainsString(
            'related=r' . RelatedSample::class . ':99',
            $canonical,
        );
    }

    public function testGenerateHashIsDeterministicAndKeyDependent(): void
    {
        $hash1 = SecurityUtility::generateHash('input');
        $hash2 = SecurityUtility::generateHash('input');

        self::assertSame($hash1, $hash2);
        self::assertSame(hash_hmac('sha256', 'input', 'unit-test-encryption-key'), $hash1);

        $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] = 'another-encryption-key';

        self::assertNotSame($hash1, SecurityUtility::generateHash('input'));
    }

    public function testVerifyPasswordRoundTrip(): void
    {
        $hash = SecurityUtility::generatePasswordHash('secret');

        self::assertTrue(SecurityUtility::verifyPassword($hash, 'secret'));
        self::assertFalse(SecurityUtility::verifyPassword($hash, 'wrong'));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] = 'unit-test-encryption-key';
    }
}
