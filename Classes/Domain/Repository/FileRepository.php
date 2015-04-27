<?php
namespace Z3\Z3Fal\Domain\Repository;


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
class FileRepository extends \TYPO3\CMS\Core\Resource\FileRepository {

	public function search($params){
		
		\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($params,'$params');
		\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this->settings,'$this->settings');
		$query = $this->createQuery();
		
		
		foreach($params as $name => $value){
			$constraints[] = $query->contains('uid', 1); 
		}
		
        $query->getQuerySettings()->setRespectStoragePage(FALSE);
		
		\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($query,'query');

		$result = $query->matching($query->logicalAnd($constraints))->execute();
		return $result;
		
		
	}
	
	/**
	 * 
	 * @param \string $name
	 * @return type
	 */
	public function findByName($name){
		
		$query = $this->createQuery();
		$constraint = $query->contains('name', $name); 
		
        $query->getQuerySettings()->setRespectStoragePage(FALSE);
		
		$result = $query->matching($constraint)->execute();
		return $result;
		
	}
	/**
	 * first simple implementation of a filter. not yet happy with the solution, but it works for now.
	 */
	public function findInCollection($searchword, $settings){
		$collectionRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\FileCollectionRepository');

		$collection = $collectionRepository->findByUid($settings['collectionUid']);
		$itemsCriteriaArray = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(':', $collection->getItemsCriteria() );
		$collection->loadContents();

		$files = $collection->getItems();
		
		if( $searchword != '' ){
			//	components of search-pattern
			if($settings['searchWordGlue'] !==''){
				$glue = "[".str_replace(',','',$settings['searchWordGlue'])."]";
			}else{
				$glue = '';
			}
			$pattern = "/".$settings['prefix']."$glue*$searchword.*/";
			
			$result = array();
			foreach($files as $file){
				if( preg_match( $pattern, $file->getName()) ){
					$result[] = $file;
				}
			}
		}else{
			$result = $files;
		}
		return $result;
	}
}