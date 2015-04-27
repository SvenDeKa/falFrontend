<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	'Files',
	'Files'
);

$pluginSignature = str_replace('_','',$_EXTKEY) . '_files';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform_files.xml');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'z3 Fal');
//
//if (!isset($GLOBALS['TCA']['sys_file']['ctrl']['type'])) {
//	if (file_exists($GLOBALS['TCA']['sys_file']['ctrl']['dynamicConfigFile'])) {
//		require_once($GLOBALS['TCA']['sys_file']['ctrl']['dynamicConfigFile']);
//	}
//	// no type field defined, so we define it here. This will only happen the first time the extension is installed!!
//	$GLOBALS['TCA']['sys_file']['ctrl']['type'] = 'tx_extbase_type';
//	$tempColumns = array();
//	$tempColumns[$GLOBALS['TCA']['sys_file']['ctrl']['type']] = array(
//		'exclude' => 1,
//		'label'   => 'LLL:EXT:z3_fal/Resources/Private/Language/locallang_db.xlf:tx_z3fal.tx_extbase_type',
//		'config' => array(
//			'type' => 'select',
//			'items' => array(),
//			'size' => 1,
//			'maxitems' => 1,
//		)
//	);
//	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_file', $tempColumns, 1);
//}
//
//$GLOBALS['TCA']['sys_file']['types']['Tx_Z3Fal_File']['showitem'] = $TCA['sys_file']['types']['1']['showitem'];
//$GLOBALS['TCA']['sys_file']['types']['Tx_Z3Fal_File']['showitem'] .= ',--div--;LLL:EXT:z3_fal/Resources/Private/Language/locallang_db.xlf:tx_z3fal_domain_model_file,';
//$GLOBALS['TCA']['sys_file']['types']['Tx_Z3Fal_File']['showitem'] .= '';
//
//$GLOBALS['TCA']['sys_file']['columns'][$TCA['sys_file']['ctrl']['type']]['config']['items'][] = array('LLL:EXT:z3_fal/Resources/Private/Language/locallang_db.xlf:sys_file.tx_extbase_type.Tx_Z3Fal_File','Tx_Z3Fal_File');
//
//\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('sys_file', $GLOBALS['TCA']['sys_file']['ctrl']['type'],'','after:' . $TCA['sys_file']['ctrl']['label']);
