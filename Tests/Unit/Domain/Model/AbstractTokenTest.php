<?php
declare(strict_types=1);

/*
 * This file is part of PSBits Security Tools.
 *
 * The copyright and license information can be found in the file LICENSE.txt,
 * which was distributed with this source code.
 */

namespace PSBits\SecurityTools\Tests\Unit\Domain\Model;

use DateTime;
use PSBits\SecurityTools\Tests\Unit\Fixtures\ConcreteToken;
use ReflectionException;
use ReflectionProperty;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class AbstractTokenTest
 *
 * @package PSBits\SecurityTools\Tests\Unit\Domain\Model
 */
class AbstractTokenTest extends UnitTestCase
{
    public function testIsExpiredIsFalseForFutureLifetime(): void
    {
        self::assertFalse((new ConcreteToken('+1 day'))->isExpired());
    }

    /**
     * @throws ReflectionException
     */
    public function testIsExpiredIsTrueForPastLifetime(): void
    {
        self::assertTrue($this->createExpiredToken()->isExpired());
    }

    public function testValidateReturnsTrueForCorrectToken(): void
    {
        $token = new ConcreteToken('+1 day');

        self::assertTrue($token->validate($token->getRawToken()));
    }

    public function testValidateReturnsFalseForWrongToken(): void
    {
        self::assertFalse((new ConcreteToken('+1 day'))->validate('wrong-token'));
    }

    /**
     * @throws ReflectionException
     */
    public function testValidateReturnsFalseWhenExpired(): void
    {
        $token = $this->createExpiredToken();

        self::assertFalse($token->validate($token->getRawToken()));
    }

    /**
     * @throws ReflectionException
     */
    private function createExpiredToken(): ConcreteToken
    {
        $token = new ConcreteToken('1 day');

        /*
         * The token lifetime is always in the future, so move the expiration date into the past to simulate an
         * expired token.
         */
        (new ReflectionProperty($token, 'expiresAt'))->setValue($token, new DateTime('-1 day'));

        return $token;
    }
}
