# Administrator Manual

## Installation

### Via Composer

Go to the directory with the composer configuration (usually project root) and run:

```bash
composer require elementareteilchen/groupdelegation
```

## Extension Configuration

Go to **Admin Tools > Settings > Extension Configuration > groupdelegation** to configure the extension.

**ignoreOrganisationUnit** (default: enabled)

When enabled, sub-admins can edit permissions for *every* non-admin BE user regardless of organisational unit. This is the default.

When disabled, sub-admins can only manage BE users that belong to the OUs assigned to their sub-admin group. This requires setting up OUs (see below).

If you change this setting later, you will have additional work reassigning TYPO3 users and their groups.

> **Important:** Using this extension, you have to take more care about the organisation of the backend groups because
> some unforeseen things might be possible. For example: if a user belongs to both the marketing and the human
> resources groups (managed by two different sub-admins), the marketing sub-admin granting news editing rights might
> inadvertently also allow editing of human resources news — if mount points are not configured carefully.

## Site Configuration

### Create Organisational Units (OU)

This step is only necessary if **ignoreOrganisationUnit** is disabled.

OUs are stored at the root level (PID 0). To create one in TYPO3 v14:

1. Open **Content > Records**
2. Click the root entry (TYPO3 logo) at the top of the page tree
3. Create a new **Organisation Unit** record — only a title is required

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
