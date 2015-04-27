<?php
      
$arguments = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_z3fal_file');

if(!is_array($arguments)){
	throw new \Exception('no arguments given');
	return;
}
/**
 * @var $TSFE \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
 */
$TSFE = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController', $TYPO3_CONF_VARS, 0, 0);
\TYPO3\CMS\Frontend\Utility\EidUtility::initLanguage();
 
// Get FE User Information
$TSFE->initFEuser();
// Important: no Cache for Ajax stuff
$TSFE->set_no_cache();
 
//$TSFE->checkAlternativCoreMethods();
$TSFE->checkAlternativeIdMethods();
$TSFE->determineId();
$TSFE->initTemplate();
$TSFE->getConfigArray();

$cmsbootstrap = \TYPO3\CMS\Core\Core\Bootstrap::getInstance();
$cmsbootstrap->loadConfigurationAndInitialize();
$cmsbootstrap->loadExtensionTables();
 
$TSFE->cObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
$TSFE->settingLanguage();
$TSFE->settingLocale();
 
/**
 * Initialize Database
 */
\TYPO3\CMS\Frontend\Utility\EidUtility::connectDB();
 
/**
 * @var $objectManager \TYPO3\CMS\Extbase\Object\ObjectManager
 */
$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
 
 
/**
 * Initialize Extbase bootstap - nt needed...
 */
//$bootstrapConf['vendor'] = 'Z3';
//$bootstrapConf['extensionName'] = 'Z3Fal';
//$bootstrapConf['pluginName'] = 'File';
// 
//$bootstrap = new TYPO3\CMS\Extbase\Core\Bootstrap();
//$bootstrap->initialize($bootstrapConf);
// 
//$bootstrap->cObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tslib_cObj');
//


/**
 * start: getting the file and deliver it.
 */
$fileHash = $arguments['hash'];
$fileUid = $arguments['file'];

$fileRepository = $objectManager->get('\TYPO3\CMS\Core\Resource\FileRepository');

// get file by hash
if($arguments['hash'] !==''){
	$file = $fileRepository->findBySha1Hash($fileHash);
}else if($arguments['file']){
	$file = $fileRepository->findByUid($fileUid);
}else {
	throw new \Exception('no identifieing parameter given');
}



if(sizeof($file)>1){
	throw new \Exception('multiple files available');
}
$fileProperties = $file[0]->getProperties();

$storage = $file[0]->getStorage();
$storageConf = $storage->getConfiguration();
$storageRec = $storage->getStorageRecord();


$filePathAndName = $storageConf['basePath'].ltrim($file[0]->getIdentifier(), '/');

if( $storageRec['driver'] == 'Local' ) {
	$filePathAndName = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($filePathAndName);
}else{
	throw new \Exception('only Local fal-driver is accepted at the moment.');
}

if( !is_readable($storageConf['basePath']) ) {
	throw new \Exception('directory \''.$storageConf['basePath'].'\' not readable');
}
if( !is_readable($filePathAndName) ) {
	throw new \Exception('file \''.$filePathAndName.'\' not readable');
}
if( !is_writeable($filePathAndName) ) {
	throw new \Exception('file \''.$filePathAndName.'\' not writeable');
}

// make sure it's a file before doing anything!
if( file_exists($filePathAndName) ) {

	// required for IE
	if(ini_get('zlib.output_compression')) {
		ini_set('zlib.output_compression', 'Off');
	}

	// get the file mime type using the file extension
//	switch(strtolower(substr(strrchr($filePathAndName, '.'), 1))) {
//		case 'pdf': $mime = 'application/pdf'; break;
//		case 'zip': $mime = 'application/zip'; break;
//		case 'jpeg':
//		case 'jpg': $mime = 'image/jpg'; break;
//		default: $mime = 'application/force-download';
//	}
	header('Pragma: public'); 	// required
	header('Expires: 0');		// no cache
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($filePathAndName)).' GMT');
	header('Cache-Control: private',false);
	header('Content-Type: '. $fileProperties['mime_type']);
	header('Content-Disposition: attachment; filename="'.basename($filePathAndName).'"');
	header('Content-Transfer-Encoding: binary');
	header('Content-Length: '.filesize($filePathAndName));	// provide file size
	header('Connection: close');
	readfile($filePathAndName);		// push it out
	exit();
//
}else{
	throw new \Exception('file \''.$filePathAndName.'\' not found');
}


?>