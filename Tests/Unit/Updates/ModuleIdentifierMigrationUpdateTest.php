<?php

declare(strict_types=1);

namespace ElementareTeilchen\Groupdelegation\Tests\Unit\Updates;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2026 Sebastian Müller <sebastian.mueller@elementare-teilchen.de>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Doctrine\DBAL\Result;
use ElementareTeilchen\Groupdelegation\Updates\ModuleIdentifierMigrationUpdate;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Upgrades\DatabaseUpdatedPrerequisite;

final class ModuleIdentifierMigrationUpdateTest extends TestCase
{
    #[Test]
    public function getTitleReturnsExpectedString(): void
    {
        $connectionPool = $this->createMock(ConnectionPool::class);
        $wizard = new ModuleIdentifierMigrationUpdate($connectionPool);

        self::assertStringContainsString('EXT:groupdelegation', $wizard->getTitle());
    }

    #[Test]
    public function getDescriptionReturnsExpectedString(): void
    {
        $connectionPool = $this->createMock(ConnectionPool::class);
        $wizard = new ModuleIdentifierMigrationUpdate($connectionPool);

        self::assertStringContainsString('site_groupdelegation', $wizard->getDescription());
        self::assertStringContainsString('groupdelegation', $wizard->getDescription());
    }

    #[Test]
    public function getPrerequisitesContainsDatabaseUpdatedPrerequisite(): void
    {
        $connectionPool = $this->createMock(ConnectionPool::class);
        $wizard = new ModuleIdentifierMigrationUpdate($connectionPool);

        self::assertSame([DatabaseUpdatedPrerequisite::class], $wizard->getPrerequisites());
    }

    #[Test]
    public function updateNecessaryReturnsFalseWhenNoLegacyRecordsExist(): void
    {
        $connectionPool = $this->createMock(ConnectionPool::class);

        $queryBuilderGroups = $this->createQueryBuilderMock('be_groups', 'groupMods', []);
        $queryBuilderUsers = $this->createQueryBuilderMock('be_users', 'userMods', []);

        $connectionPool->method('getQueryBuilderForTable')
            ->willReturnMap([
                ['be_groups', $queryBuilderGroups],
                ['be_users', $queryBuilderUsers],
            ]);
        $connectionPool->method('getConnectionForTable')
            ->willReturn($this->createMock(Connection::class));

        $wizard = new ModuleIdentifierMigrationUpdate($connectionPool);

        self::assertFalse($wizard->updateNecessary());
    }

    #[Test]
    public function updateNecessaryReturnsTrueWhenGroupRecordContainsLegacyIdentifier(): void
    {
        $connectionPool = $this->createMock(ConnectionPool::class);

        $queryBuilderGroups = $this->createQueryBuilderMock('be_groups', 'groupMods', [
            ['uid' => 1, 'groupMods' => 'web_layout,site_groupdelegation'],
        ]);
        $queryBuilderUsers = $this->createQueryBuilderMock('be_users', 'userMods', []);

        $connectionPool->method('getQueryBuilderForTable')
            ->willReturnMap([
                ['be_groups', $queryBuilderGroups],
                ['be_users', $queryBuilderUsers],
            ]);
        $connectionPool->method('getConnectionForTable')
            ->willReturn($this->createMock(Connection::class));

        $wizard = new ModuleIdentifierMigrationUpdate($connectionPool);

        self::assertTrue($wizard->updateNecessary());
    }

    #[Test]
    public function executeUpdateReplacesLegacyIdentifierAndPreservesOtherModules(): void
    {
        $connectionPool = $this->createMock(ConnectionPool::class);

        $queryBuilderGroups = $this->createQueryBuilderMock('be_groups', 'groupMods', [
            ['uid' => 10, 'groupMods' => 'web_layout,site_groupdelegation,web_list'],
        ]);
        $queryBuilderUsers = $this->createQueryBuilderMock('be_users', 'userMods', [
            ['uid' => 20, 'userMods' => 'site_groupdelegation'],
        ]);

        $connectionGroups = $this->createMock(Connection::class);
        $connectionGroups->expects(self::once())
            ->method('update')
            ->with(
                'be_groups',
                ['groupMods' => 'web_layout,groupdelegation,web_list'],
                ['uid' => 10]
            )
            ->willReturn(1);

        $connectionUsers = $this->createMock(Connection::class);
        $connectionUsers->expects(self::once())
            ->method('update')
            ->with(
                'be_users',
                ['userMods' => 'groupdelegation'],
                ['uid' => 20]
            )
            ->willReturn(1);

        $connectionPool->method('getQueryBuilderForTable')
            ->willReturnMap([
                ['be_groups', $queryBuilderGroups],
                ['be_users', $queryBuilderUsers],
            ]);

        $connectionPool->method('getConnectionForTable')
            ->willReturnMap([
                ['be_groups', $connectionGroups],
                ['be_users', $connectionUsers],
            ]);

        $wizard = new ModuleIdentifierMigrationUpdate($connectionPool);

        self::assertTrue($wizard->executeUpdate());
    }

    #[Test]
    public function executeUpdateHandlesWhitespaceAndDuplicates(): void
    {
        $connectionPool = $this->createMock(ConnectionPool::class);

        $queryBuilderGroups = $this->createQueryBuilderMock('be_groups', 'groupMods', [
            ['uid' => 15, 'groupMods' => 'web_layout, site_groupdelegation , web_layout, site_groupdelegation'],
        ]);
        $queryBuilderUsers = $this->createQueryBuilderMock('be_users', 'userMods', []);

        $connectionGroups = $this->createMock(Connection::class);
        $connectionGroups->expects(self::once())
            ->method('update')
            ->with(
                'be_groups',
                ['groupMods' => 'web_layout,groupdelegation'],
                ['uid' => 15]
            )
            ->willReturn(1);

        $connectionPool->method('getQueryBuilderForTable')
            ->willReturnMap([
                ['be_groups', $queryBuilderGroups],
                ['be_users', $queryBuilderUsers],
            ]);

        $connectionPool->method('getConnectionForTable')
            ->willReturnMap([
                ['be_groups', $connectionGroups],
                ['be_users', $this->createMock(Connection::class)],
            ]);

        $wizard = new ModuleIdentifierMigrationUpdate($connectionPool);

        self::assertTrue($wizard->executeUpdate());
    }

    private function createQueryBuilderMock(string $table, string $field, array $records): QueryBuilder
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $expr = $this->createMock(ExpressionBuilder::class);
        $result = $this->createMock(Result::class);

        $queryBuilder->method('select')->willReturnSelf();
        $queryBuilder->method('from')->willReturnSelf();
        $queryBuilder->method('where')->willReturnSelf();
        $queryBuilder->method('expr')->willReturn($expr);
        $queryBuilder->method('createNamedParameter')->willReturn(':param');
        $queryBuilder->method('escapeLikeWildcards')->willReturnArgument(0);
        $queryBuilder->method('executeQuery')->willReturn($result);

        $result->method('fetchAllAssociative')->willReturn($records);

        return $queryBuilder;
    }
}
