<?php
declare(strict_types=1);

/*
 * This file is part of PSBits Security.
 *
 * The copyright and license information can be found in the file LICENSE.txt,
 * which was distributed with this source code.
 */

namespace PSBits\Security\Tests\Unit\Fixtures;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Class ProtectedSample
 *
 * A minimal Extbase entity used to verify the handling of system fields (uid/pid) and relations in the canonical
 * representation.
 *
 * @package PSBits\Security\Tests\Unit\Fixtures
 */
class ProtectedSample extends AbstractEntity
{
    protected string      $name    = '';
    protected ?RelatedSample $related = null;

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setRelated(?RelatedSample $related): void
    {
        $this->related = $related;
    }
}
