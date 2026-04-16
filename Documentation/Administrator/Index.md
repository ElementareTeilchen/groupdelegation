# Administrator Manual

## Installation

### Via Composer

Go to the directory with the composer configuration (usually project root) and run:

```bash
composer require elementareteilchen/groupdelegation
```

### Via Extension Manager

Go to the Extension Manager, choose "Get Extensions", search for "groupdelegation" and import and install the extension.

## Extension Configuration

In the Extension Manager you can set the option **ignoreOrganisationUnit**. If selected, sub-admins are able to
edit permissions by adding/removing delegatable groups of *every BE user* who is not a TYPO3 admin. The organisation
units will be ignored.

If you change this setting later, you will have additional work with TYPO3 users and their groups.

> **Important:** Using this extension, you have to take more care about the organisation of the backend groups because
> some unforeseen things might be possible. For example: if a user belongs to both the marketing and the human
> resources groups (managed by two different sub-admins), the marketing sub-admin granting news editing rights might
> inadvertently also allow editing of human resources news — if mount points are not configured carefully.

## Site Configuration

### Create Organisational Units (OU)

This step is only necessary if the option "ignoreOrganisationUnit" is **not** set.

OUs are set up on the root page of your TYPO3 instance (uid = 0). When creating an OU, you only need to set a title.

![Create OU](../Images/create_ou.png)

### Create Sub-Admin Groups

To make a BE user group a sub-admin group, check the checkbox **"Backend Group is Sub Admin Group"** on the "Extended" tab.

If OUs are not ignored, three additional settings become available:

**1) SubAdmin can activate backend user**

If activated, a sub admin can also activate a BE user and set start/stop times for the account.

**2) Delegatable Groups**

Defines the list of backend groups which can be assigned by the sub-admin to the BE users of their OU.

**3) Editable Organisational Units**

Defines the OUs for which the sub-admin can change the backend permissions.

![Sub-admin group settings](../Images/subadmin_group.png)

The sub-admin group must also have access permissions to the modules **"User tools"** and **"User tools > Groupdelegation"**.

### Make BE Users Sub-Admins

Assign a previously defined sub-admin group to a BE user in their group settings.

### Assign BE Users to an OU

Assign BE users to OUs via the "Extended" tab of the BE user record. All users assigned to an OU can then be granted
additional permissions by a sub-admin.

![Assign BE user to OU](../Images/beuser_assign-ou.png)

### Tip

Use naming conventions when creating backend user groups. A good concept can be found at
[typo3worx.eu](https://typo3worx.eu/2017/02/typo3-backend-user-management/).