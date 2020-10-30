# TYPO3 Extension groupdelegation

Allow editors with extended rights to set group rights for other editors.
This is highly recommended for installations with a lot of backend users like on universities.

## Introduction

The TYPO3 extension **goupdelegation** provides the possibility to allow certain be-users to delegate
certain be-groups to other be-users without having a TYPO3 admin account.
This can be done using organisation units or not.
That means e.g. you can determine that sub admin xx can only delegate groups to be-users of the organisation
unit marketing.

For a detailed manual have a look at the Documentation folder.

## Screens

<img src="https://docs.typo3.org/typo3cms/extensions/groupdelegation/_images/subadmin_menu.png" />
<img src="https://docs.typo3.org/typo3cms/extensions/groupdelegation/_images/subadmin_edit-user.png" />
<img src="https://docs.typo3.org/typo3cms/extensions/groupdelegation/_images/create_ou.png" />
<img src="https://docs.typo3.org/typo3cms/extensions/groupdelegation/_images/subadmin_group.png" />

## Installation

* Install this extension - e.g. via composer `composer require in2code/groupdelegation`
* Make your global configuration in the extension manager settings
* Configure the groups as you need (look at the documentation under https://docs.typo3.org/typo3cms/extensions/groupdelegation/ for details)
* Have fun!

## Development Model

The `main` branch is the branch, which is / should be compatible with the latest TYPO3 major version. All development
should happen here.

For each old TYPO3 version there is a version compatible branch, f.e. `8LTS`. If enhancements and bugfixes should
be available in older versions they must be cherry picked from the main branch.

### Version numbers

EXT:groupdelegation follows the semantic versioning.

V8 compatible releases are tagged with `3.x.y`. V10 compatible releases are tagged with `4.y.x`.                                   |

## Support or help

* Do you want to contribute? Just do a pull request on github
* Do you need help? Just contact Marcus at https://www.in2code.de
