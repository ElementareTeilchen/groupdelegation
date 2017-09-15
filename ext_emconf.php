<?php

########################################################################
# Extension Manager/Repository config file for ext "groupdelegation".
#
# Auto generated 23-03-2010 14:52
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'groupdelegation',
	'description' => 'Allows generating Sub-Admin be_user by groups which can handle access of other be_user by groups',
	'category' => 'be',
	'author' => 'Sebastian Mueller, Bernhard Maier, Marcus Schwemer',
	'author_email' => 'sebastian.mueller@abezet.de, maier@tum.de, marcus@schwemer.de',
	'author_company' => 'A.BE.ZET GmbH, in2code GmbH',
	'conflicts' => '',
	'priority' => '',
	'module' => 'mod1',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'version' => '3.0.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '8.4.0 - 8.99.99',
            'php' => '7.0.0-7.0.99'
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'suggests' => array(
	),
);

?>
