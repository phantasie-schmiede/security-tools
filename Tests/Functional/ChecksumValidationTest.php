<?php
declare(strict_types=1);

/*
 * This file is part of PSBits Security Tools.
 *
 * The copyright and license information can be found in the file LICENSE.txt,
 * which was distributed with this source code.
 */

namespace PSBits\SecurityTools\Tests\Functional;

use PHPUnit\Framework\Attributes\IgnoreDeprecations;
use PHPUnit\Framework\Attributes\Test;
use PSBits\SecurityTools\Exception\CorruptedDataException;
use PSBitsTest\Example\Domain\Model\SecuredModel;
use PSBitsTest\Example\Domain\Repository\SecuredModelRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\Generic\Exception\NotImplementedException;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Class ChecksumValidationTest
 *
 * @package PSBits\SecurityTools\Tests\Functional
 */
#[IgnoreDeprecations]
class ChecksumValidationTest extends FunctionalTestCase
{
    protected PersistenceManager     $persistenceManager;
    protected SecuredModelRepository $securedModelRepository;

    protected array $testExtensionsToLoad = [
        'typo3conf/ext/psbits/foundation',
        'typo3conf/ext/psbitstest/example',
    ];

    /**
     * @throws IllegalObjectTypeException
     * @throws NotImplementedException
     */
    #[Test]
    public function insertRecordViaRepositoryUpdatesChecksum(): void
    {
        $newRecord = GeneralUtility::makeInstance(SecuredModel::class);
        $newRecord->setName('Test Record');
        $this->securedModelRepository->add($newRecord);
        $this->persistenceManager->persistAll();
        $this->persistenceManager->clearState();

        // Fetch the record again to check the checksum
        /** @var SecuredModel $fetchedRecord */
        $fetchedRecord = $this->securedModelRepository->findByIdentifier($newRecord->getUid());

        $this->assertSame('Test Record', $fetchedRecord->getName());
        $this->assertNotEmpty($fetchedRecord->getChecksum());
    }

    /**
     * @throws IllegalObjectTypeException
     * @throws NotImplementedException
     */
    #[Test]
    public function updateRecordDirectlyInDatabaseRaisesException(): void
    {
        $newRecord = GeneralUtility::makeInstance(SecuredModel::class);
        $newRecord->setName('Test Record');
        $this->securedModelRepository->add($newRecord);
        $this->persistenceManager->persistAll();
        $this->persistenceManager->clearState();

        // Directly update the record in the database
        $queryBuilder = $this->getConnectionPool()
            ->getQueryBuilderForTable('tx_example_domain_model_securedmodel');
        $queryBuilder->update('tx_example_domain_model_securedmodel')
            ->set('name', 'Directly Updated Test Record')
            ->where(
                $queryBuilder->expr()
                    ->eq(
                        'uid',
                        $queryBuilder->createNamedParameter($newRecord->getUid())
                    )
            )
            ->executeStatement();

        // Fetch the record again to check the checksum
        $this->expectException(CorruptedDataException::class);
        $this->expectExceptionCode(1582819384);

        /** @var SecuredModel $fetchedRecord */
        $this->securedModelRepository->findByIdentifier($newRecord->getUid());
    }

    /**
     * @throws IllegalObjectTypeException
     * @throws NotImplementedException
     * @throws UnknownObjectException
     */
    #[Test]
    public function updateRecordViaRepositoryUpdatesChecksum(): void
    {
        $newRecord = GeneralUtility::makeInstance(SecuredModel::class);
        $newRecord->setName('Test Record');
        $this->securedModelRepository->add($newRecord);
        $this->persistenceManager->persistAll();
        $initialChecksum = $newRecord->getChecksum();

        $newRecord->setName('Renamed Test Record ');
        $this->securedModelRepository->update($newRecord);
        $this->persistenceManager->persistAll();
        $this->persistenceManager->clearState();

        // Fetch the record again to check the checksum
        /** @var SecuredModel $fetchedRecord */
        $fetchedRecord = $this->securedModelRepository->findByIdentifier($newRecord->getUid());

        $this->assertSame('Renamed Test Record ', $fetchedRecord->getName());
        $this->assertNotSame($initialChecksum, $fetchedRecord->getChecksum());
    }

    /**
     * @throws IllegalObjectTypeException
     * @throws NotImplementedException
     * @throws UnknownObjectException
     */
    #[Test]
    public function addWithoutFurtherPersistAllPersistsChecksum(): void
    {
        $newRecord = GeneralUtility::makeInstance(SecuredModel::class);
        $newRecord->setName('Test Record');

        // add() must persist the checksum on its own, so no further persistAll() is triggered on purpose.
        $this->securedModelRepository->add($newRecord);
        $this->persistenceManager->clearState();

        // Fetch the record again to check the checksum
        /** @var SecuredModel $fetchedRecord */
        $fetchedRecord = $this->securedModelRepository->findByIdentifier($newRecord->getUid());

        $this->assertSame('Test Record', $fetchedRecord->getName());
        $this->assertNotEmpty($fetchedRecord->getChecksum());
    }

    /**
     * @throws IllegalObjectTypeException
     * @throws NotImplementedException
     * @throws UnknownObjectException
     */
    #[Test]
    public function changePidDirectlyInDatabaseDoesNotRaiseException(): void
    {
        $newRecord = GeneralUtility::makeInstance(SecuredModel::class);
        $newRecord->setName('Test Record');
        $this->securedModelRepository->add($newRecord);
        $this->persistenceManager->persistAll();
        $this->persistenceManager->clearState();

        // Simulate a page move by changing the pid directly in the database.
        $queryBuilder = $this->getConnectionPool()
            ->getQueryBuilderForTable('tx_example_domain_model_securedmodel');
        $queryBuilder->update('tx_example_domain_model_securedmodel')
            ->set('pid', 999)
            ->where(
                $queryBuilder->expr()
                    ->eq(
                        'uid',
                        $queryBuilder->createNamedParameter($newRecord->getUid())
                    )
            )
            ->executeStatement();

        // The pid is excluded from the checksum, so the record must still be valid.
        /** @var SecuredModel $fetchedRecord */
        $fetchedRecord = $this->securedModelRepository->findByIdentifier($newRecord->getUid());

        $this->assertSame(999, $fetchedRecord->getPid());
        $this->assertSame('Test Record', $fetchedRecord->getName());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->persistenceManager = $this->get(PersistenceManager::class);
        $this->securedModelRepository = $this->get(SecuredModelRepository::class);
    }
}
