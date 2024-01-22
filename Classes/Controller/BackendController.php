<?php

declare(strict_types=1);

namespace ElementareTeilchen\Groupdelegation\Controller;

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

use Psr\Http\Message\ResponseInterface;
use ElementareTeilchen\Groupdelegation\Utility\GroupDelegationUtility;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Backend\Attribute\Controller;

/**
 * BackendController
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */
#[Controller]
final class BackendController extends ActionController
{
    protected bool $ignoreOrganisationUnit = true;
    protected bool $editableStartStopTime = false;

    public function __construct(
        private readonly ModuleTemplateFactory $moduleTemplateFactory,
        private readonly array $groupdelegationExtensionConfiguration,
    ) {
        $this->ignoreOrganisationUnit = (bool)($groupdelegationExtensionConfiguration['ignoreOrganisationUnit'] ?? false);
        $this->editableStartStopTime = (bool)($groupdelegationExtensionConfiguration['editableStartStopTime'] ?? false);
    }

    public function indexAction(): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);

        [$isSubAdmin, $canActivateUsers, $groupIdList] = GroupDelegationUtility::getSubadminStatus();

        if ($isSubAdmin) {
            $editableUsers = GroupDelegationUtility::getEditableUsers($groupIdList, $this->ignoreOrganisationUnit, $canActivateUsers);
            $userCounter = 0;
            foreach ($editableUsers as $user) {
                $delegatableGroups = GroupDelegationUtility::getDelegatableGroups(
                    $user['uid'],
                    $groupIdList,
                    $this->ignoreOrganisationUnit,
                    $canActivateUsers
                );
                $editableUsers[$userCounter]['groups'] =
                    GroupDelegationUtility::getSeparatedGroupsOfUser(
                        $delegatableGroups,
                        $user['usergroup'] ?? ''
                    );
                $userCounter++;
            }

            $moduleTemplate->assignMultiple([
                'users'=> $editableUsers,
                'isSubAdmin' => '1',
                'canActivateUsers' => $canActivateUsers,
                'editableStartStopTime' => $this->editableStartStopTime,
            ]);
        } else {
            $moduleTemplate->assign('isSubAdmin', '0');
        }

        return $moduleTemplate->renderResponse('Backend/Index');
    }

    /**
     * @throws NoSuchArgumentException
     */
    public function editAction(int $userId): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        [$isSubAdmin, $canActivateUsers, $groupIdList] = GroupDelegationUtility::getSubadminStatus();

        $allowedToEditUser = false;
        if ($isSubAdmin) {
            $editableUsers =
                GroupDelegationUtility::getEditableUsers(
                    $groupIdList,
                    $this->ignoreOrganisationUnit,
                    $canActivateUsers
                );
            foreach ($editableUsers as $user) {
                if ($user['uid'] == $userId) {
                    $allowedToEditUser = true;
                    break;
                }
            }

            if ($allowedToEditUser == true) {
                $delegatableGroups =
                    GroupDelegationUtility::getDelegatableGroups(
                        $userId,
                        $groupIdList,
                        $this->ignoreOrganisationUnit,
                        $canActivateUsers
                    );

                $user = GroupDelegationUtility::getUserDetails($userId);

                $groupsSeparated =
                    GroupDelegationUtility::getSeparatedGroupsOfUser(
                        $delegatableGroups,
                        $user['usergroup'] ?? ''
                    );

                $userGroupsArray = explode(',', $user['usergroup'] ?? '');

                $delegatableGroupsOfUser = GroupDelegationUtility::getDelegatableGroupsOfUser($delegatableGroups);

                $moduleTemplate->assignMultiple([
                    'isSubAdmin' => '1',
                    'canActivateUsers' => $canActivateUsers,
                    'editableStartStopTime' => $this->editableStartStopTime,
                    'user' => $user,
                    'usergroups' => $userGroupsArray,
                    'delegatableGroupsOfUser' => $delegatableGroupsOfUser,
                    'groupsSeparated' => $groupsSeparated
                ]);
            }
        } else {
            $moduleTemplate->assign('isSubAdmin', '0');
        }
        return $moduleTemplate->renderResponse('Backend/Edit');

    }

    public function saveAction(int $userId, array $groups): ResponseInterface
    {
        $allowed = false;
        [$isSubAdmin, $canActivateUsers, $groupIdList] = GroupDelegationUtility::getSubadminStatus();

        if (!$isSubAdmin) {
            throw new \Exception('You are not allowed to save the user.');
        }

        $editableUsers =
            GroupDelegationUtility::getEditableUsers(
                $groupIdList,
                $this->ignoreOrganisationUnit,
                $canActivateUsers
            );
        foreach ($editableUsers as $user) {
            if ($user['uid'] == $userId) {
                $allowed = true;
                break;
            }
        }

        if ($allowed) {
            $enableFields = [];
            if ($canActivateUsers) {
                $enableFields['disable'] = $this->request->getArgument('disable');
                if ($this->editableStartStopTime) {
                    $enableFields['starttime'] = intval(strtotime($this->request->getArgument('starttime')));
                    $enableFields['endtime'] = intval(strtotime($this->request->getArgument('endtime')));
                }
            }

            $delegatableGroups = GroupDelegationUtility::getDelegatableGroups(
                $userId,
                $groupIdList,
                $this->ignoreOrganisationUnit,
                $canActivateUsers
            );

            GroupDelegationUtility::saveUser($userId, $delegatableGroups, $groups, $enableFields);
        }
        return $this->redirect('index');
    }
}
