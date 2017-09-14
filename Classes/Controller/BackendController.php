<?php
namespace In2code\Groupdelegation\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Marcus Schwemer <marcus.schwemer@in2code.de>
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

use \TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use \In2code\Groupdelegation\Utility\GroupDelegationUtility;


/**
 * BackendController
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */
class BackendController extends ActionController
{
    var $groupsSqlString = '';
    var $editableUsers = array();
    var $ignoreOrganisationUnit = '0';
    var $delegateableGroups = array();

    protected $backendUser;

    /**
     * @return void
     */
    public function initializeAction()
    {
        $extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['groupdelegation']);
        if(!isset($extConf['ignoreOrganisationUnit'])) {
            $this->ignoreOrganisationUnit = 1;
        } else {
            $this->ignoreOrganisationUnit = $extConf['ignoreOrganisationUnit'];
        }
    }

    /**
     * @return void
     */
    public function indexAction()
    {
        list($isSubAdmin, $groupIdList) = GroupDelegationUtility::isMemberOfSubAdminGroup();


        if ($isSubAdmin) {

            $editableUsers = GroupDelegationUtility::getEditableUsers($groupIdList, $this->ignoreOrganisationUnit);
            $usercount = 0;
            foreach ($editableUsers as $user) {
                $delegateableGoups = GroupDelegationUtility::getDelegateableGroups($user['uid'], $groupIdList, $this->ignoreOrganisationUnit);
                $editableUsers[$usercount]['groups'] = GroupDelegationUtility::getSeparatedGroupsOfUser($delegateableGoups, $user['usergroup']);
                $usercount++;
            }

            $this->view->assignMultiple([
                'users'=> $editableUsers,
                'isSubAdmin' => '1'
            ]);
        } else {
            $this->view->assign('isSubAdmin', '0');
        }
    }

    /**
     * @return void
     */
    public function editAction()
    {
        if ($this->request->hasArgument('user')) {
            $userId = intval($this->request->getArgument('user'));
        } else {
            $userId = 0;
        }


        list($isSubAdmin, $groupIdList) = GroupDelegationUtility::isMemberOfSubAdminGroup();

        if ($isSubAdmin) {
            $editableUsers = GroupDelegationUtility::getEditableUsers($groupIdList, $this->ignoreOrganisationUnit);
            foreach ($editableUsers as $user) {
                if($user['uid'] == $userId) {
                    $allowed = true;
                    break;
                }
            }

            if ($allowed == true) {
                $delegateableGroups = GroupDelegationUtility::getDelegateableGroups($userId, $groupIdList, $this->ignoreOrganisationUnit);

                $user = GroupDelegationUtility::getUserDetails($userId);

                $groupsSeparated = GroupDelegationUtility::getSeparatedGroupsOfUser($delegateableGroups, $user['usergroup']);

                $userGroupsArray = explode(',',$user['usergroup']);

                $delegateableGroupsOfUser = GroupDelegationUtility::getDelegateableGroupsOfUser($delegateableGroups);

                $this->view->assignMultiple([
                    'isSubAdmin' => '1',
                    'user' => $user,
                    'usergroups' => $userGroupsArray,
                    'delegateableGroupsOfUser' => $delegateableGroupsOfUser,
                    'groupsSeparated' => $groupsSeparated
                ]);

            }

        } else {
            $this->view->assign('isSubAdmin', '0');
        }
    }

    /**
     * @return void
     */
    public function saveAction()
    {
        $userId = intval($this->request->getArgument('user'));
        $shouldBeDelegated = $this->request->getArgument('groups');

        list($isSubAdmin, $groupIdList) = GroupDelegationUtility::isMemberOfSubAdminGroup();

        if ($isSubAdmin) {
            $editableUsers = GroupDelegationUtility::getEditableUsers($groupIdList, $this->ignoreOrganisationUnit);
            foreach ($editableUsers as $user) {
                if($user['uid'] == $userId) {
                    $allowed = true;
                    break;
                }
            }

            if ($allowed == true) {


                $delegateableGroups = GroupDelegationUtility::getDelegateableGroups(
                    $userId,
                    $groupIdList,
                    $this->ignoreOrganisationUnit
                );

                GroupDelegationUtility::saveUser($userId, $delegateableGroups, $shouldBeDelegated);
            }
            $this->redirect('index');
        }

    }
}
