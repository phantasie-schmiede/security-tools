<?php
declare(strict_types=1);

/*
 * This file is part of PSBits Security Tools.
 *
 * The copyright and license information can be found in the file LICENSE.txt,
 * which was distributed with this source code.
 */

namespace PSBits\SecurityTools\Tests\Unit\Fixtures;

use DateMalformedStringException;
use DateTime;
use DateTimeInterface;
use DateTimeZone;

/**
 * Class CanonicalizeSample
 *
 * @package PSBits\SecurityTools\Tests\Unit\Fixtures
 */
class CanonicalizeSample
{
    public bool              $boolValue;
    public DateTimeInterface $dateTime;
    public float             $floatValue;
    public int               $intValue;
    public ?string           $nullable;
    public string            $stringValue;

    /**
     * @throws DateMalformedStringException
     */
    public function __construct(
        ?string            $nullable = null,
        int                $intValue = 0,
        float              $floatValue = 0.0,
        string             $stringValue = '',
        bool               $boolValue = false,
        ?DateTimeInterface $dateTime = null,
    ) {
        $this->nullable = $nullable;
        $this->intValue = $intValue;
        $this->floatValue = $floatValue;
        $this->stringValue = $stringValue;
        $this->boolValue = $boolValue;
        $this->dateTime = $dateTime ?? new DateTime('2020-01-01 00:00:00', new DateTimeZone('UTC'));
    }
}
