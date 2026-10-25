<?php
declare(strict_types=1);

/*
 * This file is part of PSBits Security Tools.
 *
 * The copyright and license information can be found in the file LICENSE.txt,
 * which was distributed with this source code.
 */

namespace PSBits\SecurityTools\Tests\Unit\EventListener;

use PSBits\SecurityTools\EventListener\ValidateChecksumAfterObjectThawed;
use PSBits\SecurityTools\Exception\CorruptedDataException;
use PSBits\SecurityTools\Tests\Unit\Fixtures\ListenerSampleModel;
use PSBits\SecurityTools\Tests\Unit\Fixtures\RelatedSample;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Extbase\Event\Persistence\AfterObjectThawedEvent;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class ValidateChecksumAfterObjectThawedTest
 *
 * @package PSBits\SecurityTools\Tests\Unit\EventListener
 */
class ValidateChecksumAfterObjectThawedTest extends UnitTestCase
{
    /**
     * @throws CorruptedDataException
     */
    public function testLogsAndRethrowsOnCorruption(): void
    {
        $model  = new ListenerSampleModel(shouldThrow: true);
        $event  = new AfterObjectThawedEvent($model, []);
        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects(self::once())
            ->method('error')
            ->with(self::stringContains('simulated corruption'));

        $listener = new ValidateChecksumAfterObjectThawed($logger);

        $this->expectException(CorruptedDataException::class);
        $this->expectExceptionCode(1582819384);

        $listener($event);
    }

    /**
     * @throws CorruptedDataException
     */
    public function testDoesNotLogWhenChecksumIsValid(): void
    {
        $model  = new ListenerSampleModel(shouldThrow: false);
        $event  = new AfterObjectThawedEvent($model, []);
        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects(self::never())
            ->method('error');

        $listener = new ValidateChecksumAfterObjectThawed($logger);

        $listener($event);

        self::assertInstanceOf(ListenerSampleModel::class, $model);
    }

    /**
     * @throws CorruptedDataException
     */
    public function testIgnoresNonProtectedObjects(): void
    {
        $model  = new RelatedSample();
        $event  = new AfterObjectThawedEvent($model, []);
        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects(self::never())
            ->method('error');

        $listener = new ValidateChecksumAfterObjectThawed($logger);

        $listener($event);

        self::assertInstanceOf(RelatedSample::class, $model);
    }
}
