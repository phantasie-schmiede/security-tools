<?php
declare(strict_types=1);

/*
 * This file is part of PSBits Security.
 *
 * The copyright and license information can be found in the file LICENSE.txt,
 * which was distributed with this source code.
 */

namespace PSBits\Security\Tests\Functional;

use PHPUnit\Framework\Attributes\Test;
use PSBits\Security\Tests\Domain\Model\SecuredModel;
use PSBits\Security\Tests\Domain\Repository\SecuredModelRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Class ChecksumValidationTest
 *
 * @package PSBits\Security\Tests\Functional
 */
class ChecksumValidationTest extends FunctionalTestCase
{
    //    protected array $testExtensionsToLoad = [
    //        'typo3conf/ext/psb/psb-foundation',
    //    ];

    /**
     * @throws IllegalObjectTypeException
     */
    #[Test]
    public function insertRecordViaRepositoryUpdatesChecksum(): void
    {
        $newRecord = GeneralUtility::makeInstance(SecuredModel::class);
        $newRecord->setName('Test Record');
        $repository = GeneralUtility::makeInstance(SecuredModelRepository::class);
        $repository->add($newRecord);
        $persistenceManager = $this->get(PersistenceManager::class);
        $persistenceManager->persistAll();

        // Fetch the record again to check the checksum
        /** @var SecuredModel $fetchedRecord */
        $fetchedRecord = $repository->findByIdentifier($newRecord->getUid());
        self::assertEquals($newRecord->getName(), $fetchedRecord->getName());
    }
}
