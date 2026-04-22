After initially creating this extension and later giving it over to in2code it is now back in the hands of Elementare Teilchen.
Thanks to in2code for the good collaboration in the last years.

> We are currently using this extension with TYPO3 v14. This should also work for v13.
Use version [v5.0.7](https://github.com/ElementareTeilchen/groupdelegation/releases/tag/v5.0.7) if you need it for v12.

# TYPO3 Extension groupdelegation

Allow editors with extended rights to set group rights for other editors.
This is highly recommended for installations with a lot of backend users like on universities.

## Introduction

The TYPO3 extension **goupdelegation** provides the possibility to allow certain be-users to delegate
certain be-groups to other be-users without having a TYPO3 admin account.
This can be done using organisation units or not.
That means e.g. you can determine that sub admin xx can only delegate groups to be-users of the organisation
unit marketing.

For a detailed manual have a look at the [Documentation](Documentation/) folder.

## Support
This TYPO3 Extension is free to use. We as TUM TYPO3 Team and elementare teilchen and our Developers highly appreciate your feedback and always try to improve our Extensions.

## Screens

<img src="Documentation/Images/subadmin_menu.png" />
<img src="Documentation/Images/subadmin_edit-user.png" />
<img src="Documentation/Images/create_ou.png" />
<img src="Documentation/Images/subadmin_group.png" />

## Installation

* Install this extension - e.g. via composer `composer require elementareteilchen/groupdelegation`
* Make your global configuration in the extension manager settings
* Configure the groups as you need (look at [Documentation](Documentation/) for details)
* Have fun!

## Development Model

The `main` branch is the branch, which is / should be compatible with the latest TYPO3 major version. All development
should happen here.

### Version numbers

EXT:groupdelegation follows the semantic versioning.
Compatibility:

10 LTS: `v4.y.x`
12 LTS: `v5.y.x`
14 LTS: `v6.y.x`
