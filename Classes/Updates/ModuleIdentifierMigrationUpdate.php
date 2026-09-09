<?php

declare(strict_types=1);

namespace ElementareTeilchen\Groupdelegation\Updates;

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

use TYPO3\CMS\Core\Attribute\UpgradeWizard;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Upgrades\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

#[UpgradeWizard('groupdelegation_moduleIdentifierMigration')]
class ModuleIdentifierMigrationUpdate implements UpgradeWizardInterface
{
    private const OLD_MODULE_IDENTIFIER = 'site_groupdelegation';
    private const NEW_MODULE_IDENTIFIER = 'groupdelegation';

    /**
     * @var array<string, string>
     */
    protected array $tables = [
        'be_groups' => 'groupMods',
        'be_users' => 'userMods',
    ];

    public function __construct(
        private readonly ConnectionPool $connectionPool,
    ) {}

    public function getTitle(): string
    {
        return 'EXT:groupdelegation: Migrate module permissions to new identifier';
    }

    public function getDescription(): string
    {
        return 'Migrate backend user and group module permissions from the legacy "site_groupdelegation" identifier to "groupdelegation".';
    }

    public function getPrerequisites(): array
    {
        return [
            DatabaseUpdatedPrerequisite::class,
        ];
    }

    public function updateNecessary(): bool
    {
        return $this->migrate(true);
    }

    public function executeUpdate(): bool
    {
        return $this->migrate(false);
    }

    private function migrate(bool $dryRun): bool
    {
        $migrated = false;

        foreach ($this->tables as $table => $field) {
            $queryBuilder = $this->connectionPool->getQueryBuilderForTable($table);
            $connection = $this->connectionPool->getConnectionForTable($table);

            $queryBuilder->select('uid', $field)
                ->from($table)
                ->where(
                    $queryBuilder->expr()->like(
                        $field,
                        $queryBuilder->createNamedParameter(
                            '%' . $queryBuilder->escapeLikeWildcards(self::OLD_MODULE_IDENTIFIER) . '%'
                        )
                    )
                );

            $records = $queryBuilder->executeQuery()->fetchAllAssociative();

            foreach ($records as $record) {
                $originalModules = (string)($record[$field] ?? '');
                $modules = GeneralUtility::trimExplode(',', $originalModules, true);
                $hasLegacy = false;
                $updatedModules = [];

                foreach ($modules as $module) {
                    if ($module === self::OLD_MODULE_IDENTIFIER) {
                        $hasLegacy = true;
                        $updatedModules[] = self::NEW_MODULE_IDENTIFIER;
                    } else {
                        $updatedModules[] = $module;
                    }
                }

                if ($hasLegacy) {
                    if ($dryRun) {
                        return true;
                    }
                    $deduplicatedModules = array_unique($updatedModules);
                    $newModules = implode(',', $deduplicatedModules);
                    $connection->update(
                        $table,
                        [$field => $newModules],
                        ['uid' => (int)$record['uid']]
                    );
                    $migrated = true;
                }
            }
        }

        return $migrated;
    }
}
