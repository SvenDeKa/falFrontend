<?php
namespace Z3\Z3Fal\Controller;


/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015 Sven Külpmann <sven.kuelpmann@lenz-wie-fruehling.de>, lwf
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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

/**
 * FileController
 */
class FileController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * @var \Z3\Z3Fal\Domain\Repository\FileRepository
	 * @inject
	 */
	protected $fileRepository;
	
	/**
	 * action list
	 * 
	 * @return void
	 */
	public function listAction() {
		
		$files = \TYPO3\CMS\Core\Resource\Collection\FolderBasedFileCollection::load(
                1,
                FALSE
        );
        $this->view->assign('files', $files);
		
//		$files = $this->fileRepository->findAll();
//		$this->view->assign('files', $files);
	}

	/**
	 * action search
	 * 
	 * @return void
	 */
	public function searchAction() {
		
		$arguments = $this->request->getArguments();
		$files = NULL;
		$searchSettings = $this->settings['search'];
		
		if( $arguments['q'] !== NULL){
			$params['q'] = $arguments['q'];
			/**
			 * via findByCollection
			 * therefor we need a CollectionUid given
			 */
			if($searchSettings['collectionUid']){
				$files = $this->fileRepository->findInCollection($params['q'],$searchSettings);
			}
		}
		
		$this->view->assign('q', $arguments['q']);
		if($this->settings['directdownload'] && sizeof($files) == 1){
			$this->download($files[0]);
		}else{
			$this->view->assign('files', $files);
		}
	}

	/**
	 * action download
	 * 
	 * @return void
	 */
	public function downloadAction() {
		
	}

	/**
	 * action download
	 * 
	 * @return void
	 */
	public function download($file) {


//		$fileRepository = $objectManager->get('\TYPO3\CMS\Core\Resource\FileRepository');

		// get file by hash
//		if($arguments['hash'] !==''){
//			$file = $this->fileRepository->findBySha1Hash($fileHash);
//		}else if($arguments['file']){
//			$file = $this->fileRepository->findByUid($fileUid);
//		}else {
//			throw new \Exception('no identifieing parameter given');
//		}



		if(sizeof($file)>1){
			throw new \Exception('multiple files available');
		}
		$fileProperties = $file->getProperties();

		$storage = $file->getStorage();
		$storageConf = $storage->getConfiguration();
		$storageRec = $storage->getStorageRecord();


		$filePathAndName = $storageConf['basePath'].ltrim($file->getIdentifier(), '/');

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
	}
	

}