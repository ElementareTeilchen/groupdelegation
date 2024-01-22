<?php

declare(strict_types=1);

namespace ElementareTeilchen\Groupdelegation\Utility;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class GroupDelegationUtility
{
    /**
     * Checks whether
     * - the current be user is part of a sub-admin-group,
     * - is allowed to enable and disable users,
     * and returns this info plus a list of all sub-admin-groups, which the BE user belongs to
     */
    public static function getSubadminStatus(): array
    {
        $groupsSqlString = '';
        $groupId = [];

        $isSubAdmin = false;
        $canActivateUsers = false;

        foreach ($GLOBALS['BE_USER']->userGroups as $singleGroup) {
            if ($singleGroup['tx_groupdelegation_issubadmingroup'] === 1) {
                $isSubAdmin = true;
                if ($singleGroup['tx_groupdelegation_canactivate'] === 1) {
                    $canActivateUsers = true;
                }
                $groupId[] = $singleGroup['uid'];
            }
        }
        if ($isSubAdmin) {
            $groupsSqlString = implode(',', $groupId);
        }

        return [$isSubAdmin, $canActivateUsers, $groupsSqlString];
    }

    public static function getEditableUsers(
        string $groupsSqlString,
        bool $ignoreOrganisationUnit,
        bool $canActivateUsers
    ): array {
        if ($ignoreOrganisationUnit === true) {
            $editableUsers = self::getEditableUsersIgnoreOU();
        } else {
            $editableUsers = self::getEditableUsersRespectOU($canActivateUsers, $groupsSqlString);
        }
        return $editableUsers;
    }

    private static function getEditableUsersRespectOU(bool $canActivateUsers, string $groupsSqlString): array
    {
        $select = 'SELECT be_users.uid,be_users.username,be_users.usergroup,be_users.realName ';
        $from = ' FROM be_groups INNER JOIN tx_groupdelegation_begroups_organisationunit_mm ON be_groups.uid = tx_groupdelegation_begroups_organisationunit_mm.uid_local
			INNER JOIN tx_groupdelegation_organisationunit ON tx_groupdelegation_begroups_organisationunit_mm.uid_foreign = tx_groupdelegation_organisationunit.uid
			INNER JOIN tx_groupdelegation_beusers_organisationunit_mm ON tx_groupdelegation_organisationunit.uid = tx_groupdelegation_beusers_organisationunit_mm.uid_foreign
			INNER JOIN be_users ON tx_groupdelegation_beusers_organisationunit_mm.uid_local = be_users.uid';
        $where =  ' WHERE be_groups.uid in (' . $groupsSqlString . ') ';
        $where .= ' AND be_users.admin = 0';
        $where .= ' AND be_users.deleted = 0';
        $where .= ' AND tx_groupdelegation_organisationunit.hidden = 0 AND tx_groupdelegation_organisationunit.deleted = 0 ';
        $where .= ' AND be_groups.hidden = 0 AND be_groups.deleted = 0';
        if (!$canActivateUsers) {
            $where .= ' AND be_users.disable = 0';
            $where .= ' AND (be_users.starttime < ' . GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('date', 'timestamp') . ' OR be_users.starttime = 0)';
            $where .= ' AND (be_users.endtime > ' . GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('date', 'timestamp') . ' OR be_users.endtime = 0)';
        }
        $groupBy = ' GROUP BY be_users.uid ';
        $orderBy = ' ORDER BY be_users.username ';

        return self::getConnection('be_groups')->executeQuery(
            $select .
            $from .
            $where .
            $groupBy .
            $orderBy
        )->fetchAllAssociative();
    }

    private static function getEditableUsersIgnoreOU(): array
    {
        $queryBuilder = self::getQueryBuilderForTable('be_users');
        return $queryBuilder
            ->select('uid', 'username', 'usergroup', 'realName')
            ->from('be_users')
            ->where(
                $queryBuilder->expr()->eq('admin', 0),
                $queryBuilder->expr()->eq('deleted', 0)
            )
            ->orderBy('username')
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /**
     * Sets instance variable delegatableGroups to an array of all delegatable groups from sub admin to $userId
     */
    public static function getDelegatableGroups(
        int $userId,
        string $groupsSqlString,
        bool $ignoreOrganisationUnit = true,
        bool $canActivateUsers = false
    ): array {
        $delegatableGroups = [];

        if ($ignoreOrganisationUnit) {
            $select = 'SELECT delg.uid_foreign';
            $from = ' FROM be_groups INNER JOIN tx_groupdelegation_subadmin_begroups_mm delg ON be_groups.uid = delg.uid_local';
            $where = ' WHERE delg.uid_local IN (' . $groupsSqlString . ')';
            $where .= ' AND be_groups.deleted = 0 AND be_groups.hidden = 0';
        } else {
            $select = 'SELECT delg.uid_foreign';
            $from = ' FROM be_users
				INNER JOIN tx_groupdelegation_beusers_organisationunit_mm beuou ON be_users.uid = beuou.uid_local
				INNER JOIN tx_groupdelegation_organisationunit ON beuou.uid_foreign = tx_groupdelegation_organisationunit.uid
				INNER JOIN tx_groupdelegation_begroups_organisationunit_mm begou ON tx_groupdelegation_organisationunit.uid = begou.uid_foreign
				INNER JOIN be_groups begroupssubadmin ON begou.uid_local = begroupssubadmin.uid
				INNER JOIN tx_groupdelegation_subadmin_begroups_mm delg ON begroupssubadmin.uid = delg.uid_local
				INNER JOIN be_groups ON be_groups.uid = delg.uid_foreign';
            $where = ' WHERE be_users.uid = ' . $userId;
            $where .= ' AND delg.uid_local IN (' . $groupsSqlString . ')';
            if (!$canActivateUsers) {
                $where .= ' AND be_users.disable = 0';
                $where .= ' AND (be_users.starttime < ' . GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('date', 'timestamp') . ' OR be_users.starttime = 0)';
                $where .= ' AND (be_users.endtime > ' . GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('date', 'timestamp') . ' OR be_users.endtime = 0)';
            }
            $where .= ' AND be_users.deleted = 0';
            $where .= ' AND tx_groupdelegation_organisationunit.deleted = 0 AND tx_groupdelegation_organisationunit.hidden = 0';
            $where .= ' AND begroupssubadmin.hidden = 0 AND begroupssubadmin.deleted =0';
        }

        $groupBy = ' GROUP BY delg.uid_foreign';
        $orderBy = '';
        $statement = self::getConnection('be_groups')->executeQuery(
            $select .
            $from .
            $where .
            $groupBy .
            $orderBy
        );
        while ($row = $statement->fetchAssociative()) {
            $delegatableGroups[] = $row['uid_foreign'];
        }
        return $delegatableGroups;
    }

    /**
     * Builds an array with a string of all delegatable and a string of all not delegatable groups
     */
    public static function getSeparatedGroupsOfUser(
        array $delegatableGroups,
        string $currentUserGroupsString = ''
    ): array {
        $groups = [];

        if ($currentUserGroupsString == '') {
            $currentUserGroupsArray = [];
        } else {
            $currentUserGroupsArray = explode(',', $currentUserGroupsString);
        }

        $notDelegatable = array_diff($currentUserGroupsArray, $delegatableGroups);
        $allGroupIdsForUser = array_merge($notDelegatable, $delegatableGroups);
        $allGroupIdsForUserString = implode(',', array_filter($allGroupIdsForUser));

        $queryBuilder = self::getQueryBuilderForTable('be_groups');
        $statement = $queryBuilder
            ->select('uid', 'title')
            ->from('be_groups')->where($queryBuilder->expr()->in('uid', $allGroupIdsForUserString))->executeQuery();

        while ($row = $statement->fetchAssociative()) {
            if (in_array($row['uid'], $notDelegatable)) {
                $groups['hasNotDelegatable'][$row['uid']] = $row['title'];
            } elseif (in_array($row['uid'], $currentUserGroupsArray)) {
                $groups['hasDelegatable'][$row['uid']] = $row['title'];
            }
        }
        return $groups;
    }

    /**
     * Saves the assigned / removed groups in the edit form to the user. Includes checks if sub admin is allowed to
     * do this changes.
     *
     * @param int $userId
     * @param array $delegatableGroups
     * @param array $shouldBeDelegated
     * @param array $enableFields
     * @return void
     */
    public static function saveUser(
        int $userId,
        array $delegatableGroups,
        array $shouldBeDelegated,
        array $enableFields
    ) {
        $user = self::getUserDetails($userId);
        $userGroupsArray = explode(',', $user['usergroup'] ?? '');
        $notDelegatable = array_diff($userGroupsArray, $delegatableGroups);

        $saveAllowed = [];
        foreach ($shouldBeDelegated as $shouldGroup) {
            if (in_array($shouldGroup, $delegatableGroups)) {
                $saveAllowed[] = $shouldGroup;
            }
        }

        $doSave = array_merge($notDelegatable, $saveAllowed);
        $doSave = implode(',', $doSave);

        $fields_values = [
            'usergroup' => $doSave,
            // tstamp to know when be_user record was last updated
            'tstamp' => time()
        ];
        if (isset($enableFields['disable'])) {
            $fields_values['disable'] = intval($enableFields['disable']);
        }
        if (isset($enableFields['starttime'])) {
            $fields_values['starttime'] = $enableFields['starttime'];
        }
        if (isset($enableFields['endtime'])) {
            $fields_values['endtime'] = $enableFields['endtime'];
        }

        $queryBuilder = self::getQueryBuilderForTable('be_users');
        $queryBuilder->update('be_users')
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($userId))
            );
        foreach ($fields_values as $field => $value) {
            $queryBuilder->set($field, $value);
        }
        $queryBuilder->executeStatement();
    }

    public static function getUserDetails(int $userId): array
    {
        $user = [];

        $fields = ['uid', 'username', 'usergroup', 'realName', 'disable', 'starttime', 'endtime'];
        $queryBuilder = self::getQueryBuilderForTable('be_users');
        $statement = $queryBuilder
            ->select(...$fields)
            ->from('be_users')->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($userId)))->executeQuery();
        if ($row = $statement->fetchAssociative()) {
            $user = $row;
        }
        return $user;
    }

    public static function getDelegatableGroupsOfUser(array $delegatableGroups): array
    {
        $delegatableGroupsOfUser = [];

        $groupList = implode(',', $delegatableGroups);

        if (!empty($groupList)) {
            $queryBuilder = self::getQueryBuilderForTable('be_groups');
            $statement = $queryBuilder
                ->select('uid', 'title')
                ->from('be_groups')->where($queryBuilder->expr()->in('uid', $groupList))->executeQuery();

            while ($row = $statement->fetchAssociative()) {
                $delegatableGroupsOfUser[$row['uid']] = $row['title'];
            }
        }
        return $delegatableGroupsOfUser;
    }

    private static function getQueryBuilderForTable(string $table): QueryBuilder
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($table);
        $queryBuilder->getRestrictions()->removeAll();
        return $queryBuilder;
    }

    protected static function getConnection(string $table): Connection
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable($table);
    }
}
