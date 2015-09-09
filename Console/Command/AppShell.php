<?php
/**
 * AppShell file
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v 2.0
 */

App::uses('Shell', 'Console');

/**
 * Application Shell
 *
 * Add your application-wide methods in the class below, your shells
 * will inherit them.
 *
 * @package       app.Console.Command
 */
class AppShell extends Shell {
	
	public function initialize()
	{
		parent::initialize();
		$this->loadModel('Gallery');
		$this->loadModel('ApertureConnector.Place');
		$this->loadModel('ApertureConnector.Version');
		$this->loadModel('ApertureConnector.IptcProperty');
		
	}
	
	public function main()
	{
		$this->out('Indexing versions...');
		
		$findversionOptions = array(
				'limit' => 20,
				'page' => 1,
				'conditions' => array(
						'Version.isFlagged' => 1,
						'Version.showInLibrary' => 1,
						'Version.isHidden' => 0,
						'Version.isInTrash' => 0,
						'Version.mainRating >=' => 0
				),
				'contain' => array(
						'PlaceForVersion' => array(
								'fields' => array('placeId')
						),
						'Keyword' => array(
								'fields' =>  array('modelId', 'name'),
						)),
				'order' => 'Version.imageDate ASC',
				'fields' => array('Version.encodedUuid', 'Version.name', 'Version.exifLatitude', 'Version.exifLongitude', 'Version.unixImageDate', 'Version.stackUuid'),
				'group' => array('Version.encodedUuid', 'Version.name', 'Version.exifLatitude', 'Version.exifLongitude', 'Version.unixImageDate', 'Version.stackUuid'),
		);
		
		$placeIds = array();
		
		
		while($versions = $this->Version->find('all', $findversionOptions)){
			
			foreach ($versions as $version){
				//$this->out('Treat version '.$version['Version']['modelId']);
				$key = '/versionId:'.$version['Version']['modelId'];
				$this->Gallery->locale = DEFAULT_LANGUAGE;
				$gallery = $this->Gallery->find('first', array(
						'conditions' => array('url' => $key),
						'recursive' => 0,
				));
				
				if(!$gallery){				
					$model = array(
							'name' => $version['Version']['name'],
							'url' => '/versionId:'.$version['Version']['modelId'],
							'counter' => 1,
							'thumbUuid' => $version['Version']['encodedUuid'],
							'published' => true,
							'weight' => 99,
							'zip' => '',
							'homepage' => false,
							'keyword' => false,
							'generated' => true,
					);
					$this->Gallery->create();
					$gallery = $this->Gallery->save($model);
					if($gallery){
						//$this->out('Version gallery created...');
					}
						
						//print_r($gallery); exit;
				}
				
				if($gallery && $gallery['Gallery']['generated']){
					$this->IptcProperty->contain('UniqueString');
					$iptcProperty = $this->IptcProperty->find('all', array(
							'conditions' => array(
									'versionid' => $version['Version']['modelId'],
									'propertyKey' => array('ObjectName'),
									'stringProperty not null'
							)
					));
					if($iptcProperty){
						$gallery['Gallery']['name'] = $iptcProperty[0]['UniqueString']['stringProperty'];
						//$this->out('Update gallery for stack with the name '.$iptcProperty[0]['UniqueString']['stringProperty']);
						foreach (Configure::read('Config.languages') as $lng => $language){
							$this->Gallery->locale = $lng;		
							$this->Gallery->save($gallery);
						}
					}
				}
				
				
				foreach ($version['PlaceForVersion'] as &$place){
					if(isset($placesId[$place['placeId']])){						
						++$placesId[$place['placeId']]['counter'];
					}
					else {
						$placesId[$place['placeId']] = array(
								'counter' => 1,
								'icon' => $version['Version']['encodedUuid']
						);						
					}
				}
				
				
			}
			++$findversionOptions['page'];	
			

		}
		
		
		$this->out('Indexing locations...');
		
		//print_r($placesId);
		
		$locations = $this->Place->find('all', array(
				'contain' => array(
						'PlaceName' => array(
								'conditions' => array('PlaceName.language' => array_keys (Configure::read('Config.languages'))),
						)
				),
				'conditions' => array(
						'Place.modelId' => array_keys($placesId),
				),
		));
		
		
		foreach($locations as $location){
			if(isset($placesId[$location['Place']['modelId']])){

				$this->Gallery->locale = DEFAULT_LANGUAGE;
				$gallery = $this->Gallery->findByUrl('/placeId:'.$location['Place']['modelId']);
				
				
				if(!$gallery){
					$model = array(
							'name' => $location['Place']['defaultName'],
							'url' => '/placeId:'.$location['Place']['modelId'],
							'counter' => $placesId[$location['Place']['modelId']]['counter'],
							'thumbUuid' => $placesId[$location['Place']['modelId']]['icon'],
							'published' => true,
							'weight' => 99,
							'zip' => '',
							'homepage' => false,
							'keyword' => false,
							'generated' => true,
					);
					$this->Gallery->create();
					$gallery = $this->Gallery->save($model);
					if($gallery){
						//$this->out('Location gallery created...');
					}
				}
				
				
				if($gallery){
					$gallery['Gallery']['counter'] = $placesId[$location['Place']['modelId']]['counter'];
					
					if($gallery['Gallery']['generated']){
						foreach (Configure::read('Config.languages') as $lng => $language){
							$found = false;
							foreach($location['PlaceName'] as $placeName){
								if(strcmp($lng, $placeName['language']) == 0){
									$found = true;
									$gallery['Gallery']['name'] = $placeName['description'];
									break;
								}
							}
							if(!$found){
								$gallery['Gallery']['name'] = $location['Place']['defaultName'];
							}
							$this->Gallery->locale = $lng;
							$this->Gallery->save($gallery);
							//$this->out('Location gallery updated for '.$lng.' with the name '.$gallery['Gallery']['name']);
						
						}						
					}
					else{
						$this->Gallery->save($gallery);					
					}
					
				}
			}
		}
	}
	

}
