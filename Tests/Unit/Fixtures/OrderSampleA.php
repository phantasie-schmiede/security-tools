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
 * Class OrderSampleA
 *
 * Declares its properties in a specific order. Used together with OrderSampleB to verify that the canonical
 * representation does not depend on the property declaration order.
 *
 * @package PSBits\Security\Tests\Unit\Fixtures
 */
class OrderSampleA
{
    public function __construct(
        public string $alpha = 'a',
        public string $beta  = 'b',
    ) {
    }
}
