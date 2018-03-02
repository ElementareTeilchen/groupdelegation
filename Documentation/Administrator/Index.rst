.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


=======================
Administrator Manual
=======================

Installation
==============

Via Extension Manager
----------------------

Go to the Extension Manager, choose "Get Extensions", search for "groupdelegation" and finally import and install the
extension "groupdelegation".

Via composer
-------------

Go to the directory with the composer configuration, which is usually project root. Then fire this command:

.. code ::

   composer require in2code-de/groupdelegation

In case you want to use the repository version, you need to add the following lines to the repository section of your
projects composer.json.

.. code ::

   {
      "type": "git",
      "url": "https://github.com/in2code-de/groupdelegation"
   }

Extension Configuration
=========================

In the EM you can set the option "ignoreOrganisationUnit". If you select it, sub-admins are able to edit permissions
by adding/removing delegateable groups of *every BE user*, who is not a TYPO3 admin. The organisation units will be
ignored.

If you change it later, you will have additional work with TYPO3 users and their groups.

If you set ignoreOrganisationUnit, sub-admins are able to edit the rights of every non TYPO3 admin BE-user by
adding/removing their delegateable groups and not just be-users with a specific OU.

**Important**

Using this extension, you have to take more care about the organisation of the backend groups because
some unforseen things might be possible. Think you have a group that contains the rights to edit news (list-module,
allowed tables, exclude fields). You also have two groups that allow access to different parts of the page tree (one
for the marketing department and one for the human resources department). Imagine user xx contains the marketing and
the human resources group (managed by two different sub-admins). Now the marketing sub-admin wants to allow that user
to edit the marketing news. That might enable him also to edit the human resources news, if the mount points aren't
configured carefully – although the human resources sub admin didn't explicitly allow that.


Site Configuration
=====================

Create Organisational Units (OU)
---------------------------------------

This step is only necessary if the option in the EM "ignoreOrganisationUnit" is not set.

OUs are set up on the root page of your TYPO3 instance (uid = 0). When creating an OU, you can only set a title for it.
Nothing more is required or possible.

.. image:: /Images/create_ou.png
   :width: 750px


Create Sub-Admin-Groups
-------------------------

In order to make a BE user group a sub-admin group, you need to check the checkbox "Backend Group is Sub Admin Group"
on the tab "extended".

If you do not ignore OUs, you will have three more settings available. If not, the OU setting is not visible.

**1) SubAdmin can activate backend user**

If this checkbox is activated, a sub admin can also activate a be user and set start and stop time for the account.

**2) Delegateable Groups**

This defines the list of backend groups, which can be assigned by the sub-admin to the BE users of his OU.

**3) Editable Organisational Units**

This defines the OUs for which the sub-admin can change the backend permissions.

.. image:: /Images/subadmin_group.png
   :width: 750px

Furthermore the Sub-admin group must have access permissions to the modules “User tools” and
“User tools > Groupdelegation”.

Make BE users to Sub-admins
-----------------------------

This is easy as to assign a BE user group to a BE user. Just go to the settings and assign a previously defined
sub-admin group.

Assign BE users to an OU
--------------------------

The last step is to assign BE users to OUs. This is done in the tab "Extended". All users, which are assigned to an OU,
can now be granted additional permissions by a sub-admin.

.. image:: /Images/beuser_assign-ou.png
   :width: 750px

Tipp
-------

You should really use naming conventions, when creating backend user groups. This might keep some troubles away. A good
concept can be found at `typo3worx.eu <https://typo3worx.eu/2017/02/typo3-backend-user-management/>`_
