<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
$TCA["tx_groupdelegation_organisationunit"] = array (
	"ctrl" => $TCA["tx_groupdelegation_organisationunit"]["ctrl"],
	"interface" => array (
			"showRecordFieldList" => "hidden,title"
	),
	"feInterface" => $TCA["tx_groupdelegation_organisationunit"]["feInterface"],
	"columns" => array (
		'hidden' => array (
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'title' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:groupdelegation/locallang_db.xml:tx_groupdelegation_organisationunit.title',
			'config' => array (
				'type' => 'input',
				'size' => '30',
			)
		),
	),
	"types" => array (
		"0" => array("showitem" => "hidden;;1;;1-1-1, title;;;;2-2-2")
	),
	"palettes" => array (
		"1" => array("showitem" => "")
	)
);



?>