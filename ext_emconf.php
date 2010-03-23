<?php

########################################################################
# Extension Manager/Repository config file for ext: "groupdelegation"
#
# Auto generated 26-01-2010 14:57
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'groupdelegation',
	'description' => 'Allows generating Sub-Admin be_user by groups which can handle access of other be_user by groups',
	'category' => 'module',
	'author' => 'Sebastian Mueller, Bernhard Maier',
	'author_email' => 'sebastian.mueller@abezet.de, maier@tum.de',
	'shy' => '',
	'dependencies' => 'cms',
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
	'author_company' => '',
	'version' => '0.0.0',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:21:{s:9:"ChangeLog";s:4:"a8a0";s:10:"README.txt";s:4:"ee2d";s:21:"ext_conf_template.txt";s:4:"6081";s:12:"ext_icon.gif";s:4:"6144";s:14:"ext_tables.php";s:4:"6b95";s:14:"ext_tables.sql";s:4:"71e3";s:21:"icon_be_sub_admin.gif";s:4:"6ac3";s:44:"icon_tx_groupdelegation_organisationunit.gif";s:4:"ef32";s:16:"locallang_db.xml";s:4:"25e7";s:7:"tca.php";s:4:"9f6c";s:14:"doc/manual.sxw";s:4:"771f";s:19:"doc/wizard_form.dat";s:4:"4aaf";s:20:"doc/wizard_form.html";s:4:"57e5";s:33:"mod1/class.tx_groupdelegation.php";s:4:"d61f";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"c478";s:14:"mod1/index.php";s:4:"a55e";s:18:"mod1/locallang.xml";s:4:"719e";s:22:"mod1/locallang_mod.xml";s:4:"6a33";s:19:"mod1/moduleicon.gif";s:4:"94a2";s:14:"mod1/style.css";s:4:"1ff0";}',
	'suggests' => array(
	),
);

?>