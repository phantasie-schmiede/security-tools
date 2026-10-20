<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Security.
 *
 * The copyright and license information can be found in the file LICENSE.txt,
 * which was distributed with this source code.
 */

namespace PSBits\Security\Domain\Repository;

use Doctrine\DBAL\ParameterType;
use PSBits\Foundation\Service\Configuration\TcaService;
use PSBits\Security\Domain\Model\AbstractToken;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Class AbstractTokenRepository
 *
 * Base repository for tokens derived from \PSBits\Security\Domain\Model\AbstractToken. It offers bulk operations that
 * are not efficiently expressible via the Extbase query API, most notably the cleanup of expired tokens.
 *
 * Single use tokens should be removed right after a successful validation, e.g. by calling removeBySelector() with the
 * selector the token was looked up by.
 *
 * @template T of AbstractToken
 * @extends Repository<T>
 * @package PSBits\Security\Domain\Repository
 */
abstract class AbstractTokenRepository extends Repository
{
    /**
     * Removes the token with the given selector.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function removeBySelector(string $selector): void
    {
        $tableName    = $this->getTableName();
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($tableName);

        $queryBuilder->delete($tableName)
            ->where(
                $queryBuilder->expr()
                    ->eq(
                        'selector',
                        $queryBuilder->createNamedParameter($selector),
                    ),
            )
            ->executeStatement();
    }

    /**
     * Removes all tokens that have expired.
     *
     * @return int The number of removed tokens.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function removeExpired(): int
    {
        $tableName    = $this->getTableName();
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($tableName);

        return $queryBuilder->delete($tableName)
            ->where(
                $queryBuilder->expr()
                    ->lt(
                        'expires_at',
                        $queryBuilder->createNamedParameter(time(), ParameterType::INTEGER),
                    ),
            )
            ->executeStatement();
    }

    /**
     * Returns the database table the tokens of this repository are stored in. It is derived from the managed model
     * class via the foundation TcaService and can be overridden if the derivation does not match.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    protected function getTableName(): string
    {
        return GeneralUtility::makeInstance(TcaService::class)
            ->convertClassNameToTableName($this->objectType);
    }
}
