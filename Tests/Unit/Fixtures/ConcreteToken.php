<?php
declare(strict_types=1);

/*
 * This file is part of PSBits Security Tools.
 *
 * The copyright and license information can be found in the file LICENSE.txt,
 * which was distributed with this source code.
 */

namespace PSBits\SecurityTools\Tests\Unit\Fixtures;

use PSBits\SecurityTools\Domain\Model\AbstractToken;

/**
 * Class ConcreteToken
 *
 * A concrete token for unit tests, allowing the lifetime to be controlled (e.g. in the past for expired tokens).
 *
 * @package PSBits\SecurityTools\Tests\Unit\Fixtures
 */
class ConcreteToken extends AbstractToken
{
    public function __construct(string $lifetime = '+1 day')
    {
        parent::__construct($lifetime);
    }
}
