<?php
namespace In2code\Groupdelegation\Utility;

/**
 * Class GroupDelegationUtility
 *
 * @author in2marcus
 * @package In2code\Groupdelegation\Utility
 */
class GroupDelegationUtility
{

    /**
     *
     * Checks whether the current be user is part of a sub-admin-group and returns a list of
     * all sub-admin-groups, which the BE user belongs to
     *
     * @return array
     */
    public static function isMemberOfSubAdminGroup() :array
    {

        $groupsSqlString = '';
        $groupId = [];

        $isSubAdmin = false;

        foreach ($GLOBALS['BE_USER']->userGroups as $singleGroup) {
            if($singleGroup['tx_groupdelegation_issubadmingroup']=== 1) {
                $isSubAdmin = true;
                $groupId[] =  $singleGroup['uid'];
            }
        }
        if($isSubAdmin===true) {
            $groupsSqlString = implode(',',$groupId);
        }

        return array($isSubAdmin, $groupsSqlString);

    }

    /**
     *
     * Returns the list of users, who can be edited by the current be user
     *
     * @return	array
     */
    public static function getEditableUsers($groupsSqlString, $ignoreOrganisationUnit = 1) : array
    {

        // make sure to start with an empty array if called twice
        $editableUsers = [];

        if($ignoreOrganisationUnit == 1) {
            $table = 'be_users';
            $where = 'be_users.admin = 0';
            $groupBy = '';
        } else {
            $table = 'be_groups';
            $table .= ' INNER JOIN tx_groupdelegation_begroups_organisationunit_mm ON be_groups.uid = tx_groupdelegation_begroups_organisationunit_mm.uid_local
			INNER JOIN tx_groupdelegation_organisationunit ON tx_groupdelegation_begroups_organisationunit_mm.uid_foreign = tx_groupdelegation_organisationunit.uid
			INNER JOIN tx_groupdelegation_beusers_organisationunit_mm ON tx_groupdelegation_organisationunit.uid = tx_groupdelegation_beusers_organisationunit_mm.uid_foreign
			INNER JOIN be_users ON tx_groupdelegation_beusers_organisationunit_mm.uid_local = be_users.uid';
            $where = 'be_groups.uid in ('.$groupsSqlString.')';
            $where .= ' AND be_users.admin = 0';
            $where .=  \TYPO3\CMS\Backend\Utility\BackendUtility::BEenableFields('tx_groupdelegation_organisationunit') . \TYPO3\CMS\Backend\Utility\BackendUtility::deleteClause('tx_groupdelegation_organisationunit');
            $where .=  \TYPO3\CMS\Backend\Utility\BackendUtility::BEenableFields('be_groups') . \TYPO3\CMS\Backend\Utility\BackendUtility::deleteClause('be_groups');
            $groupBy = 'be_users.uid';
        }
        $where .=  \TYPO3\CMS\Backend\Utility\BackendUtility::BEenableFields('be_users') . \TYPO3\CMS\Backend\Utility\BackendUtility::deleteClause('be_users');


        $orderBy = 'be_users.username';
        $limit = '';
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
            'be_users.uid,be_users.username,be_users.usergroup,be_users.realName',
            $table,
            $where,
            $groupBy,
            $orderBy,
            $limit
        );

        while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))  {
            $editableUsers[] = $row;
        }
        $GLOBALS['TYPO3_DB']->sql_free_result($res);

        return $editableUsers;
    }

    /**
     * Sets instance variable delegateableGroups to an array of all delegateable groups from sub admin to $userId
     *
     * @param	int		$userId: uid of backend user record
     * @return	array
     */
    public static function getDelegateableGroups($userId, $groupsSqlString, $ignoreOrganisationUnit = 1) : array
    {

        $userId = intval($userId);
        $delegateableGroups = [];

        if($ignoreOrganisationUnit) {
            $select = 'delg.uid_foreign';
            $table = 'be_groups
				INNER JOIN tx_groupdelegation_subadmin_begroups_mm delg ON be_groups.uid = delg.uid_local';
            $where = 'delg.uid_local IN (' . $groupsSqlString . ')';
            $where .=  \TYPO3\CMS\Backend\Utility\BackendUtility::BEenableFields('be_groups') . \TYPO3\CMS\Backend\Utility\BackendUtility::deleteClause('be_groups');

        } else {
            $select = 'delg.uid_foreign';
            $table = 'be_users
				INNER JOIN tx_groupdelegation_beusers_organisationunit_mm beuou ON be_users.uid = beuou.uid_local
				INNER JOIN tx_groupdelegation_organisationunit ON beuou.uid_foreign = tx_groupdelegation_organisationunit.uid
				INNER JOIN tx_groupdelegation_begroups_organisationunit_mm begou ON tx_groupdelegation_organisationunit.uid = begou.uid_foreign
				INNER JOIN be_groups begroupssubadmin ON begou.uid_local = begroupssubadmin.uid
				INNER JOIN tx_groupdelegation_subadmin_begroups_mm delg ON begroupssubadmin.uid = delg.uid_local
				INNER JOIN be_groups ON be_groups.uid = delg.uid_foreign';
            $where = 'be_users.uid = ' . $userId;
            $where .= ' AND delg.uid_local IN (' . $groupsSqlString . ')';
            $where .= \TYPO3\CMS\Backend\Utility\BackendUtility::BEenableFields('be_users') . \TYPO3\CMS\Backend\Utility\BackendUtility::deleteClause('be_users');
            $where .= \TYPO3\CMS\Backend\Utility\BackendUtility::BEenableFields('tx_groupdelegation_organisationunit') . \TYPO3\CMS\Backend\Utility\BackendUtility::deleteClause('tx_groupdelegation_organisationunit');
            $enableClauseBeGroups = \TYPO3\CMS\Backend\Utility\BackendUtility::BEenableFields('be_groups') . \TYPO3\CMS\Backend\Utility\BackendUtility::deleteClause('be_groups');
            $where .= str_replace('be_groups','begroupssubadmin',$enableClauseBeGroups);
            $where .= $enableClauseBeGroups;
        }

        $groupBy = 'delg.uid_foreign';
        $orderBy = '';
        $limit = '';
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
            $select,
            $table,
            $where,
            $groupBy,
            $orderBy,
            $limit
        );
        while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))  {
            $delegateableGroups[] = $row['uid_foreign'];
        }
        $GLOBALS['TYPO3_DB']->sql_free_result($res);

        return $delegateableGroups;
    }

    /**
     * Builds an array with a string of all delegateable and a string of all not delegateable groups
     *
     * @param	string		$currentUserGroupsString: comma seperated list of all groups the user belongs to
     * @return	array		array with a string for delegateable and not delegateable groups of current user
     */
    public static function getSeparatedGroupsOfUser($delegateableGroups, $currentUserGroupsString = '') : array
    {

        $groups = [];

        if($currentUserGroupsString == '') {
            $currentUserGroupsArray = array();
        } else {
            $currentUserGroupsArray = explode(',',$currentUserGroupsString);
        }

        $notDelegateable = array_diff($currentUserGroupsArray,$delegateableGroups);
        $allGroupIdsForUser = array_merge($notDelegateable,$delegateableGroups);
        $allGroupIdsForUserString = implode(',',$allGroupIdsForUser);

        $select = 'uid, title';
        $table = 'be_groups';
        $where = 'uid IN (' . $allGroupIdsForUserString . ')';
        $where .= \TYPO3\CMS\Backend\Utility\BackendUtility::BEenableFields('be_groups') . \TYPO3\CMS\Backend\Utility\BackendUtility::deleteClause('be_groups');
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
            $select,
            $table,
            $where
        );
        $groupsDelegateable = array();
        $groupsNotDelegateable = array();

        while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))  {
            if(in_array($row['uid'],$notDelegateable)) {
                $groups['hasNotDelegateable'][$row['uid']] = $row['title'];
            } elseif(in_array($row['uid'],$currentUserGroupsArray)) {
                $groups['hasDelegateable'][$row['uid']] = $row['title'];
            }
        }
        $GLOBALS['TYPO3_DB']->sql_free_result($res);

        return $groups;
    }

    /**
     * Saves the assigned / removed groups in the edit form to the user. Includes checks if sub admin is allowed to do this changes.
     *
     * @return	void
     */
    public static function saveUser(int $userId, array $delegateableGroups, array $shouldBeDelegated)
    {
        $user = GroupDelegationUtility::getUserDetails($userId);

        $userGroupsArray = explode(',',$user['usergroup']);

        $notDelegateable = array_diff($userGroupsArray, $delegateableGroups);

        $saveAllowed = [];
        if(is_array($shouldBeDelegated)) {
            foreach($shouldBeDelegated as $shouldGroup) {
                if(in_array($shouldGroup,$delegateableGroups)) {
                    $saveAllowed[] = $shouldGroup;
                }
            }
        }

        $doSave = array_merge($notDelegateable,$saveAllowed);
        $doSave = implode(',', $doSave);


        $table = 'be_users';
        $where = 'uid='.$userId;
        $fields_values = array(
            'usergroup' => $doSave,
            // tstamp to know when be_user record was last updated
            'tstamp' => time()
        );
        // have to be done via exec_UPDATEquery since if sub admin is no TYPO3 admin the user has no permission for $tce->process_datamap();
        $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
            $table,
            $where,
            $fields_values
        );

    }


    /**
     * @param $userID
     * @return void
     */
    public static function getUserDetails($userId) : array
    {

        $user = [];

        $select = 'uid,username,usergroup,realName';
        $table = 'be_users';
        $where = 'uid = '.$userId;
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
            $select,
            $table,
            $where
        );
        if($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
            $user = $row;
        }
        $GLOBALS['TYPO3_DB']->sql_free_result($res);

        return $user;
    }

    public static function getDelegateableGroupsOfUser($delegateableGroups) : array
    {
        $delegateableGroupsOfUser = [];

        $grouplist = implode(',',$delegateableGroups);

        if(!empty($grouplist)) {
            $select = 'uid, title';
            $table = 'be_groups';
            $where = 'uid IN (' . $grouplist . ')';

            $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                $select,
                $table,
                $where
            );
            while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
                $delegateableGroupsOfUser[$row['uid']] = $row['title'];
            }
            $GLOBALS['TYPO3_DB']->sql_free_result($res);
        }

        return $delegateableGroupsOfUser;

    }
}
