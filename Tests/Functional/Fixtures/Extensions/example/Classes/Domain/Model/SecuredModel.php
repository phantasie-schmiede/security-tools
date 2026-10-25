<?php
declare(strict_types=1);

/*
 * This file is part of PSBits Security Tools.
 *
 * The copyright and license information can be found in the file LICENSE.txt,
 * which was distributed with this source code.
 */

namespace PSBitsTest\Example\Domain\Model;

use PSBits\Foundation\Attribute\TCA\ColumnType\Input;
use PSBits\Foundation\Attribute\TCA\Ctrl;
use PSBits\SecurityTools\Domain\Model\AbstractProtectedModel;

/**
 * Class SecuredModel
 *
 * @package PSBitsTest\Example\Domain\Model
 */
#[Ctrl]
class SecuredModel extends AbstractProtectedModel
{
    #[Input]
    protected string $name = '';

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
