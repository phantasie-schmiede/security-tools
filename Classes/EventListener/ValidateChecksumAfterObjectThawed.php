<?php
declare(strict_types=1);

/*
 * This file is part of PSBits Security.
 *
 * The copyright and license information can be found in the file LICENSE.txt,
 * which was distributed with this source code.
 */

namespace PSBits\Security\EventListener;

use PSBits\Security\Domain\Model\DataManipulationProtectionInterface;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Extbase\Event\Persistence\AfterObjectThawedEvent;

/**
 * Class ValidateChecksumAfterObjectThawed
 *
 * @package PSBits\Security\EventListener
 */
#[AsEventListener(identifier: 'security/validate-checksum-after-object-thawed')]
final readonly class ValidateChecksumAfterObjectThawed
{
    public function __invoke(AfterObjectThawedEvent $event): void
    {
        $object = $event->getObject();

        if (!$object instanceof DataManipulationProtectionInterface) {
            return;
        }

        $object->validateChecksum();
    }
}
