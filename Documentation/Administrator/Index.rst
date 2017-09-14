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

In the EM you can set the option "ignoreOrganisationUnit". If you selected it, subadmins are able to edit permissions
by adding/removing delegateable groups of *every BE user*, who is not a TYPO3 admin. The organisation units will be
ignored.

If you change it later, you will have additional work with TYPO3 users and their groups.

If you set ignoreOrganisationUnit, sub admins are able to edit the rights of every non TYPO3 admin BE-user by
adding/removing their delegateable groups and not just be-users with a specific OU.

*Important*

Using this extension you have to take more care about the organisation of the backend groups because
some unforseen things might be possible. Think you have a group that contains the rights to edit news (list-module,
allowed tables, exclude fields). You also have two groups that allow access to different parts of the page tree (one
for the marketing department and one for the human resources department). Imagine user xx contains the marketing and
the human resources group (managed by two different sub admins). Now the marketing sub admin wants to allow that user
to edit the marketing news. That might enable him also to edit the human resources news, if the mount points aren't
configured carefully – although the human resources sub admin didn't explicitly allow that.


Configuration
===============

Setup Organisational Units (optional)
---------------------------------------

Using OUs
---------

First of all you should create your OUs on the root page of your site.

Now if you create a be-user you are able to assign one or more of your OUs to the be-user.

Now simply create a be-group. By checking “Backend Group is Sub Admin Group” in the tab “Extended” you specify this
group to be a sub admin group. Select the delegateable groups of this sub admin and assign all OUs this sub admin is
responsible for.

Note: Sub admin groups must have access to the modules “User tools” and “User tools>groupdelegation”.

Ignore OUs
----------

Just create a be-group and specify it as sub admin group. Select the delegateable groups of this sub admin group.

Note: Sub admin groups must have access to the modules “User tools” and “User tools>groupdelegation”.

Tipp
~~~~~

Using naming conventions in sub admin groups might prevent you from using a sub admin groups as delegateable group.

Creating Sub Admins
-------------------

Just assign the  sub admin group you created earlier to the be-users you want to be able to delegate groups.

Configuration Reference
=======================

Extension Manager configuration
-------------------------------

No further configuration, see administration
