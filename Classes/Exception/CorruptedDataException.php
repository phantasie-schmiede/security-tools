<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Security.
 *
 * The copyright and license information can be found in the file LICENSE.txt,
 * which was distributed with this source code.
 */

namespace PSBits\Security\Exception;

use PSB\PsbFoundation\Exceptions\BaseException;

/**
 * Class CorruptedDataException
 *
 * This exception should be thrown when the checksum of a data object does not match the expected value,
 * indicating that the data has been corrupted or tampered with.
 *
 * @package PSBits\Security\Exception
 */
class CorruptedDataException extends BaseException
{
}
