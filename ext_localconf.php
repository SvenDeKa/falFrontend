<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

$TYPO3_CONF_VARS['FE']['eID_include']['fal_download'] = 'EXT:z3_fal/Classes/Service/Download.php';

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Z3.' . $_EXTKEY,
	'Files',
	array(
		'File' => 'search, list',
		
	),
	// non-cacheable actions
	array(
		'File' => 'search, download',
	)
);
