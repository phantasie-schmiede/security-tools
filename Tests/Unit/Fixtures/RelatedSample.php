<?php
declare(strict_types=1);

/*
 * This file is part of PSBits Security Tools.
 *
 * The copyright and license information can be found in the file LICENSE.txt,
 * which was distributed with this source code.
 */

namespace PSBits\SecurityTools\Tests\Unit\Fixtures;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Class RelatedSample
 *
 * A minimal entity used as a relation target to verify that relations are canonicalized by "ClassName:uid".
 *
 * @package PSBits\SecurityTools\Tests\Unit\Fixtures
 */
class RelatedSample extends AbstractEntity
{
}
