<?php
declare(strict_types=1);

/*
 * This file is part of PSBits Security.
 *
 * The copyright and license information can be found in the file LICENSE.txt,
 * which was distributed with this source code.
 */

namespace PSBits\Security\Tests\Unit\Fixtures;

/**
 * Class OrderSampleB
 *
 * Declares the same properties as OrderSampleA but in a different order.
 *
 * @package PSBits\Security\Tests\Unit\Fixtures
 */
class OrderSampleB
{
    public function __construct(
        public string $beta  = 'b',
        public string $alpha = 'a',
    ) {
    }
}
