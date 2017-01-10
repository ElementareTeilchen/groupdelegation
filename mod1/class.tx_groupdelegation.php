<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Sebastian Mueller <sebastian.mueller@abezet.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
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


/**
 * Module 'groupdelegation' for the 'groupdelegation' extension.
 *
 * @author	Sebastian Mueller <sebastian.mueller@abezet.de>
 * @package	TYPO3
 * @subpackage	tx_groupdelegation
 */
class  tx_groupdelegation {
	var $groupsSqlString = '';
	var $editableUsers = array();
	var $ignoreOrganisationUnit = '0';
	var $delegateableGroups = array();

	function __construct() {
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['groupdelegation']);
		if(!isset($extConf['ignoreOrganisationUnit'])) {
			$this->ignoreOrganisationUnit = 1;
		} else {
			$this->ignoreOrganisationUnit = $extConf['ignoreOrganisationUnit'];
		}
	}

	/**
	 * Builds an overview of all editable backend users depending on the sub admin viewing it
	 *
	 * @return	string		HTML modul content, list of all editable backend users with links to edit them
	 */
	function renderUserOverview()	{
		$content .= '<table class="userlist-groupdelegation">
									<tr class="head-line">
										<th class="username">' . $GLOBALS['LANG']->getLL('username') . '</th>
										<th class="icon">&nbsp;</th>
										<th class="name">' . $GLOBALS['LANG']->getLL('name') . '</th>
										<th class="allowed">' . $GLOBALS['LANG']->getLL('overview_delegateable') . '</th>
										<th class="not-allowed">' . $GLOBALS['LANG']->getLL('overview_not_delegateable') . '</th>
									</tr>';

		foreach($this->editableUsers as $user) {
			$this->setDelegateableGroups($user['uid']);
			$content .= '<tr class="body-line">';
			$content .= '<td class="username"><a href="index.php?user=' . $user['uid'] . '">' . $user['username'] . '</a></td>';
			$content .= '<td class="icon"><a href="index.php?user=' . $user['uid'] . '"><img alt="" title="Edit record" ' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/edit2.gif') . '></a></td>';
			$content .= '<td class="name">' . $user['realName'] . '</td>';
			$groups = $this->getAllGroupNamesOfUser($user['usergroup']);
			$content .= '<td class="allowed">' . $groups['hasDelegateable'] . '</td>';
			$content .= '<td class="not-allowed">' . $groups['hasNotDelegateable'] .'</td>';
			$content .= '</tr>';
		}
		$content .= '</table>';
		return $content;
	}

	/**
	 * Builds edit user form for user set via post parameter. 
	 *
	 * @return	string		HTML modul content, edit user form including a select field with all delegateable groups and a string of not delegateable groups depending on the sub admin viewing it
	 */
	function renderEditUserForm() {
		$userId = intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('user'));
		$allowed = false;
		foreach($this->editableUsers as $user) {
			if($user['uid'] == $userId) {
				$allowed = true;
			}
		}

		if($allowed === true) {
			$this->setDelegateableGroups($userId);

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


			$groupStringUser = $user['usergroup'];

			$groupNamesSeperated = $this->getAllGroupNamesOfUser($groupStringUser);

			if($groupStringUser=='') {
				$currentUserGroupsArray = array();
			} else {
				$currentUserGroupsArray = explode(',',$groupStringUser);
			}

			$content .= '<span class="current-user"><img alt="" title="User" ' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/i/be_users.gif') . '>&nbsp;' . $user['username'] . '&nbsp;-&nbsp;' . $user['realName'] . '&nbsp;[' . $user['uid'] . ']</span><br /><br />';

			if(!empty($groupNamesSeperated['hasNotDelegateable'])){
				$content .= '<div class="float-container">';
			}
			$stringDelegateableGroupsOfUser = implode(',',$this->delegateableGroups);
			if(!empty($stringDelegateableGroupsOfUser)) {
				$select = 'uid, title';
				$table = 'be_groups';
				$where = 'uid IN (' . $stringDelegateableGroupsOfUser . ')';

				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
					$select,
					$table,
					$where
				);
				$groupNames = array();
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))  {
					$groupNames[$row['uid']] = $row['title'];
				}
				$GLOBALS['TYPO3_DB']->sql_free_result($res);

				$content .= '<h4>' .$GLOBALS['LANG']->getLL('delegateable') . '</h4>';
				$content .= '<div class="edit-select-container"><input name="user" type="hidden" value="' . $userId . '" /><input name="edit" type="hidden" value="edit" />';

				$content .= '<select name="delegatedGroups[]" size="10" multiple="multiple">';
				foreach($this->delegateableGroups as $groupDel) {
					if(in_array($groupDel,$currentUserGroupsArray)) {
						$content .= '<option value="' . $groupDel . '" selected="selected">' . $groupNames[intval($groupDel)] . '</option>';
					} else {
						$content .= '<option value="' . $groupDel . '">' . $groupNames[intval($groupDel)] . '</option>';
					}
				}
				$content .= '</select><br />' . $GLOBALS['LANG']->getLL('labels.holdDownCTRL') . '<br /><br /><input type="submit" value="' . $GLOBALS['LANG']->getLL('save_button') . '" /></div>';
			} else {
				$content.= '<br />' . $GLOBALS['LANG']->getLL('no_delegateable_group');
			}
			if(!empty($groupNamesSeperated['hasNotDelegateable'])){
				$content .= '</div><div class="edit-groups-not-allowed"><h4>' .$GLOBALS['LANG']->getLL('not_delegateable') . '</h4>';
				$content .= $groupNamesSeperated['hasNotDelegateable'] . '<div style="clear:both;"></div>';
			}
		}
		return $content;
	}

	/**
	 * Saves the assigned / removed groups in the edit form to the user. Includes checks if sub admin is allowed to do this changes.
	 *
	 * @return	void		
	 */
	function save() {
		$userId = intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('user'));
		$allowed = false;
		foreach($this->editableUsers as $user) {
			if($user['uid'] == $userId) {
				$allowed = true;
			}
		}
		if($allowed === true) {
			$this->setDelegateableGroups($userId);

			$select = 'usergroup';
			$table = 'be_users';
			$where = 'uid = '.$userId;
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
				$select,
				$table,
				$where
			);
			if($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$groupStringUser = $row['usergroup'];
			}

			$GLOBALS['TYPO3_DB']->sql_free_result($res);

			if(!empty($groupStringUser)) {
				$groupIdsUser = explode(',',$groupStringUser);
			} else {
				$groupIdsUser = array();
			}

			$notDelegateable = array_diff($groupIdsUser,$this->delegateableGroups);

			$shouldBeDelegated = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('delegatedGroups');
			$saveAllowed = array();

			if(is_array($shouldBeDelegated)) {
				foreach($shouldBeDelegated as $shouldGroup) {
					if(in_array($shouldGroup,$this->delegateableGroups)) {
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
	}

	/**
	 * Checks if sub admin is in any sub admin group and sets the instance variable groupSqlString to a comma seperated list of all assigned sub admin groups
	 *
	 * @return	boolean		true if sub admin is in any sub admin group
	 */
	function checkAdminAndSetAdminGroupsSqlString() {
		$isSubAdmin = false;
			//my sub admin groups
		foreach ($GLOBALS['BE_USER']->userGroups as $singleGroup) {
			if($singleGroup['tx_groupdelegation_issubadmingroup']=== '1') {
				$isSubAdmin = true;
				$groupId[]=  $singleGroup['uid'];
			}
		}
		if($isSubAdmin===true) {
			$this->groupsSqlString = implode(',',$groupId);
		}

		return $isSubAdmin;
	}

	/**
	 * Sets instance variabel editableUsers to an array of all users the sub admin is allowed to edit
	 *
	 * @return	void		
	 */
	function setEditableUsers() {
			// make sure to start with an empty array if called twice
		$this->editableUsers = array();
		if($this->ignoreOrganisationUnit == 1) {
			$table = 'be_users';
			$where = 'be_users.admin = 0';
			$groupBy = '';
		} else {
			$table = 'be_groups';
			$table .= ' INNER JOIN tx_groupdelegation_begroups_organisationunit_mm ON be_groups.uid = tx_groupdelegation_begroups_organisationunit_mm.uid_local
			INNER JOIN tx_groupdelegation_organisationunit ON tx_groupdelegation_begroups_organisationunit_mm.uid_foreign = tx_groupdelegation_organisationunit.uid
			INNER JOIN tx_groupdelegation_beusers_organisationunit_mm ON tx_groupdelegation_organisationunit.uid = tx_groupdelegation_beusers_organisationunit_mm.uid_foreign
			INNER JOIN be_users ON tx_groupdelegation_beusers_organisationunit_mm.uid_local = be_users.uid';
			$where = 'be_groups.uid in ('.$this->groupsSqlString.')';
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
		$rows = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))  {
			$this->editableUsers[] = $row;
		}
		$GLOBALS['TYPO3_DB']->sql_free_result($res);
	}

	/**
	 * Sets instance variable delegateableGroups to an array of all delegateable groups from sub admin to $userId
	 *
	 * @param	int		$userId: uid of backend user record
	 * @return	void		
	 */
	function setDelegateableGroups($userId) {
		$userId = intval($userId);
		#$GLOBALS['TYPO3_DB']->debugOutput = TRUE;
		if($this->ignoreOrganisationUnit) {
			$select = 'delg.uid_foreign';
			$table = 'be_groups
				INNER JOIN tx_groupdelegation_subadmin_begroups_mm delg ON be_groups.uid = delg.uid_local';
			$where = 'delg.uid_local IN (' . $this->groupsSqlString . ')';
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
			$where .= ' AND delg.uid_local IN (' . $this->groupsSqlString . ')';
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
		$this->delegateableGroups = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))  {
			$this->delegateableGroups[] = $row['uid_foreign'];
		}
		$GLOBALS['TYPO3_DB']->sql_free_result($res);
	}

	/**
	 * Builds an array with a string of all delegateable and a string of all not delegateable groups 
	 *
	 * @param	string		$currentUserGroupsString: comma seperated list of all groups the user belongs to
	 * @return	array		array with a string for delegateable and not delegateable groups of current user
	 */
	function getAllGroupNamesOfUser($currentUserGroupsString = '') {
		if($currentUserGroupsString == '') {
			$currentUserGroupsArray = array();
		} else {
			$currentUserGroupsArray = explode(',',$currentUserGroupsString);
		}

		$notDelegateable = array_diff($currentUserGroupsArray,$this->delegateableGroups);
		$allGroupIdsForUser = array_merge($notDelegateable,$this->delegateableGroups);
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
				$groupsNotDelegateable[$row['uid']] = $row['title'];
			} elseif(in_array($row['uid'],$currentUserGroupsArray)) {
				$groupsDelegateable[$row['uid']] = $row['title'];
			}
		}
		$GLOBALS['TYPO3_DB']->sql_free_result($res);
		$groups['hasNotDelegateable'] = implode(',',$groupsNotDelegateable);
		$groups['hasDelegateable'] = implode(',',$groupsDelegateable);

		return $groups;
	}

}
?>
