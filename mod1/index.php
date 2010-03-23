<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Sebastian Mueller <sebastian.mueller@abezet.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/


	// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require_once('conf.php');
require_once($BACK_PATH.'init.php');
require_once($BACK_PATH.'template.php');

$GLOBALS['LANG']->includeLLFile('EXT:groupdelegation/mod1/locallang.xml');
$GLOBALS['LANG']->includeLLFile('EXT:lang/locallang_core.xml');
require_once(PATH_t3lib.'class.t3lib_scbase.php');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]
require_once(t3lib_extMgm::extPath('groupdelegation', 'mod1/class.tx_groupdelegation.php'));


/**
 * Module 'groupdelegation' for the 'groupdelegation' extension.
 *
 * @author	Sebastian Mueller <sebastian.mueller@abezet.de>
 * @package	TYPO3
 * @subpackage	tx_groupdelegation
 */
class  tx_groupdelegation_module1 extends t3lib_SCbase {
	var $pageinfo;

	/**
	 * Initializes the Module
	 *
	 * @return	void
	 */
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		parent::init();

		/*
		if (t3lib_div::_GP('clear_all_cache'))	{
			$this->include_once[] = PATH_t3lib.'class.t3lib_tcemain.php';
		}
		*/
	}

	/**
	 * Main function of the module. Write the content to $this->content
	 *
	 * @return	void
	 */
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

			// Draw the header.
		$this->doc = t3lib_div::makeInstance('noDoc');
		$this->doc->styleSheetFile2 = $GLOBALS["temp_modPath"] . 'style.css';
		$this->doc->backPath = $BACK_PATH;
		$this->doc->form='<form action="" method="POST">';

			// JavaScript
		$this->doc->JScode = '
			<script language="javascript" type="text/javascript">
				script_ended = 0;
				function jumpToUrl(URL)	{
					document.location = URL;
				}
			</script>
		';
		$this->doc->postCode='
			<script language="javascript" type="text/javascript">
				script_ended = 1;
				if (top.fsMod) top.fsMod.recentIds["web"] = 0;
			</script>
		';

		$headerSection = $this->doc->getHeader('pages',$this->pageinfo,'');

		$this->content.=$this->doc->startPage($LANG->getLL('title'));
		$this->content.=$this->doc->header($LANG->getLL('title'));
		$this->content.=$this->doc->spacer(5);
		$this->content.=$this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
		$this->content.=$this->doc->divider(5);


			// Render content:
		$this->moduleContent();


			// ShortCut
		if ($BE_USER->mayMakeShortcut())	{
			$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
		}

		$this->content.=$this->doc->spacer(10);
	}

	/**
	 * Prints out the module HTML
	 *
	 * @return	void
	 */
	function printContent()	{
		$this->content.=$this->doc->endPage();
		echo $this->content;
	}

	/**
	 * Generates the module content
	 *
	 * @return	void
	 */
	function moduleContent()	{
		$mod = t3lib_div::makeInstance('tx_groupdelegation');

		if($mod->checkAdminAndSetAdminGroupsSqlString() === false) {
			$this->content .= $GLOBALS['LANG']->getLL('no_permission');
		} else {
			$mod->setEditableUsers();

				//build edit user form
			if(t3lib_div::GPvar('user') && !t3lib_div::GPvar('edit')) {
				$this->content.=$this->doc->section($GLOBALS['LANG']->getLL('edit_user'),$mod->renderEditUserForm(),0,1);

				//save and build user overview
			} elseif(t3lib_div::GPvar('user') && t3lib_div::GPvar('edit')) {
				$mod->save();
				$mod->setEditableUsers();
				$this->content .= $this->doc->section($GLOBALS['LANG']->getLL('editable_user_overview'),$mod->renderUserOverview(),0,1);

					//build user overview
			} else {
					$this->content .= $this->doc->section($GLOBALS['LANG']->getLL('editable_user_overview'),$mod->renderUserOverview(),0,1);
			}
		}
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/groupdelegation/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/groupdelegation/mod1/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_groupdelegation_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>