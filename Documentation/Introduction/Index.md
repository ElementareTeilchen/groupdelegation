# Introduction

## What does it do?

The extension "groupdelegation" provides the possibility to allow certain BE users to delegate certain BE groups to
other BE users without having a TYPO3 admin account. This can be done using organisation units or not. That means e.g.
you can determine that sub admin xx can only delegate groups to BE users of the organisation unit marketing.

The extension "groupdelegation" was developed by Sebastian Müller for "Technische Universität München". The idea and
most of the conceptional part were done by Bernhard Maier <maier@tum.de>.

## Concept

By default groupdelegation uses organisation units (in the following referred to as OU). Using these means that
sub admins can only edit BE users that belong to the OUs the sub admin is responsible for. You can assign every BE user
to one or more OUs.

A BE user is a sub admin, if a sub admin group is assigned to him/her. Every sub admin group must also be assigned to
an OU in order to be able to delegate groups to users of the same OU.