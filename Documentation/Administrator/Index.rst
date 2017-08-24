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

Import and install the extension using the Extension Manager.

After installing you can set the option ignoreOrganisationUnit in the extension manager. If you change this setting
later you may have some extra work with the groups and users.

By default groupdelegation doesn't use organisation units (in the following referred to as OU). Using these means that
sub admins can only edit be-users that belong to the OUs the sub admin is responsible for. You can assign every be-user
to one or more OUs.

A be-user is a sub admin, if a sub admin group is assigned to him/her. Every sub admin group must also be assigned to
a OU in order to be able to delegate groups to users of the same OU.

If you set ignoreOrganisationUnit, sub admins are able to edit the rights of every non TYPO3 admin be-user by
adding/removing their delegateable groups and not just be-users with a specific OU.

Important: Using this extension you have to take more care about the organisation of the backend groups because
some unforseen things might be possible. Think you have a group that contains the rights to edit news (list-module,
allowed tables, exclude fields). You also have two groups that allow access to different parts of the page tree (one
for the marketing department and one for the human resources department). Imagine user xx contains the marketing and
the human resources group (managed by two different sub admins). Now the marketing sub admin wants to allow that user
to edit the marketing news. That might enable him also to edit the human resources news, if the mount points aren't
configured carefully – although the human resources sub admin didn't explicitly allow that.


Preparation (Creating Sub Admin Groups)
=======================================

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
