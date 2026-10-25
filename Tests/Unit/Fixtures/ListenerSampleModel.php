<?php
declare(strict_types=1);

/*
 * This file is part of PSBits Security Tools.
 *
 * The copyright and license information can be found in the file LICENSE.txt,
 * which was distributed with this source code.
 */

namespace PSBits\SecurityTools\Tests\Unit\Fixtures;

use PSBits\SecurityTools\Domain\Model\AbstractProtectedModel;
use PSBits\SecurityTools\Exception\CorruptedDataException;

/**
 * Class ListenerSampleModel
 *
 * A protected model whose validateChecksum() can be forced to throw, simulating a corrupted record.
 *
 * @package PSBits\SecurityTools\Tests\Unit\Fixtures
 */
class ListenerSampleModel extends AbstractProtectedModel
{
    public function __construct(
        private readonly bool $shouldThrow = false,
    ) {
    }

    public function validateChecksum(): void
    {
        if ($this->shouldThrow) {
            throw new CorruptedDataException('simulated corruption', 1582819384);
        }
    }
}
