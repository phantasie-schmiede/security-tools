<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Security Tools.
 *
 * The copyright and license information can be found in the file LICENSE.txt,
 * which was distributed with this source code.
 */

namespace PSBits\SecurityTools\Domain\Model;

/**
 * Interface DataManipulationProtectionInterface
 *
 * @package PSBits\SecurityTools\Domain\Model
 */
interface DataManipulationProtectionInterface
{
    public function calculateChecksum(bool $store): string;

    public function validateChecksum(): void;
}
