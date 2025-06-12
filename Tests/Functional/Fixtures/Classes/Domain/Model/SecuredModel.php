<?php
declare(strict_types=1);

/*
 * This file is part of PSBits Security.
 *
 * The copyright and license information can be found in the file LICENSE.txt,
 * which was distributed with this source code.
 */

namespace PSBits\Security\Tests\Domain\Model;

use PSB\PsbFoundation\Attribute\TCA\ColumnType\Input;
use PSBits\Security\Domain\Model\AbstractProtectedModel;

/**
 * Class SecuredModel
 *
 * @package PSBits\Security\Tests\Domain\Model
 */
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
