<?php
use Elasticsearch\Client;

class ApertureController extends AppController {

	public $uses = array('ApertureConnector.ApertureConnectorAppModel', 'Gallery', 'ApertureConnector.Version', 'ApertureConnector.Keyword', 'ApertureConnector.KeywordForVersion', 'ApertureConnector.PlaceName', 'ApertureConnector.Place', 'ApertureConnector.IptcProperty', 'ApertureConnector.OtherProperty', 'ApertureConnector.ExifStringProperty', 'ApertureConnector.ExifNumberProperty', 'ApertureConnector.PlaceForVersion', 'ApertureConnector.Album');
	public $components = array('RequestHandler', 'Paginator');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('count', 'gallery', 'map', 'markers', 'price', 'search');
	}
	
	public function count() {
		return $this->Version->find('count', array(
				'conditions' => array(
						'Version.showInLibrary' => 1,
						'Version.isHidden' => 0,
						'Version.isInTrash' => 0,
						'Version.mainRating >=' => 0,
				),
		));
	}
	
	
	
	private function getKeywords(){
		$rootKeywordId = Configure::read('rootKeywordId');
		return $this->getAllSubKeywords($rootKeywordId);
	}
	
	
	
	private function getAllSubKeywords($keywordId){
		$parentids = array($keywordId);		
		$findOptions = array(
				'conditions' => array(
						'parentid' => array($parentids)
				),
				'order' => 'searchname'
		);
		
		$parentFound = 0;
		$keywords = array();
		
		while($parentFound != count($parentids)){
		
			$parentFound = count($parentids);
			$findOptions['conditions']['parentid'] = $parentids;
			$keywords = $this->Keyword->find('list',$findOptions);
			$parentids = array_keys($keywords);
			$parentids[] = $keywordId;
		}
		
		$keywords[$keywordId] = $this->Keyword->findByModelid($keywordId)['Keyword']['name'];
		//print_r($keywords); exit;
		return $keywords;
	}
	
	
	
	private function getAllParentsKeywords(&$arrayKeys){
		$keys = $arrayKeys;
		while(true){
			$count = count($arrayKeys);
			$findOptions = array(
					'conditions' => array(
							'modelid' => $keys
					),
					'fields' => array('parentid', 'name')
			);

			$keywords = $this->Keyword->find('list',$findOptions);
			if($keywords){
				$keys = array_keys($keywords);
				$arrayKeys = array_merge($keys, $arrayKeys);
			}
			else {
				break;
			}
		}
		return $arrayKeys;
	}
	
	public function keywords(){		
		
		$apertureKeywords = $this->getKeywords();
		//print_r($apertureKeywords); exit;
		if( count($apertureKeywords) <= 0 ){
			throw new NotFoundException();
		}
		
		$this->Gallery->locale = Configure::read('Config.language');
		$galleries = array();
		foreach ($apertureKeywords as $modelId => $keyword){
			$gallery = $this->Gallery->find('first', array(
					'conditions' => array('url' => '/keywordId:'.$modelId)
			));
			
// 			echo $modelId.'<br>';
// 			print_r($gallery); exit;
			if($gallery){
				$galleries[$modelId] = $gallery;
				//print_r($galleries);	
			}
		}
		//echo 'coucou'; exit;
		/*$keywords = array();
		$this->Gallery->locale = Configure::read('Config.language');
		foreach ($apertureKeywords as $id => $apertureKeyword) {
			$keyword = array('Keyword' => array('id'=>$id, 'name'=>$apertureKeyword));
			$gallery = $this->Gallery->find('first', array('conditions'=>array('url'=>'/keywordId:'.$id)));
			if($gallery){
				$keyword['Gallery'] = &$gallery['Gallery'];
			}
			$keywords = &$keyword;
		}*/
		
		//print_r($galleries, '<br>'); exit;

		$this->set('models', $apertureKeywords);
		$this->set('model', 'keywordId');
		$this->set('galleries', $galleries);
		$this->view = 'gallery-list';
				

	}

	
	public function buildFindversionOptions(){
	
		$findversionOptions = array(
		// 				'limit' => 20,
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
								'fields' =>  array('modelId'),
						)),
				'order' => array('Version.imageDate DESC'),
				//'fields' => array('Version.imageDate', 'Version.encodedUuid', 'Version.name', 'Version.exifLatitude', 'Version.exifLongitude', 'Version.unixImageDate', 'Version.stackUuid'),
				//'group' => array( 'Version.imageDate', 'Version.encodedUuid', 'Version.name', 'Version.exifLatitude', 'Version.exifLongitude', 'Version.unixImageDate', 'Version.stackUuid'),
		);
	
		if(isset($this->request->params['named']['album'])){
			$album = $this->getAlbum($this->Version->decodeUuid($this->request->params['named']['album']));
			// 			$title .= __(' de l\'album %s', $album['name']);
			$findversionOptions['joins'][] = array(
					'table' => 'RKAlbumVersion',
					'alias' => 'AlbumVersion',
					'type' => 'inner',
					'conditions' => array(
							'AlbumVersion.versionId = version.modelId',
							'AlbumVersion.albumId' => $album['modelId'],
					));
		}
		if(isset($this->request->params['named']['keyword'])){
			$keywords = $this->getKeywords($this->request->params['named']['keyword']);
			$ids = array();
			foreach ($keywords as $key => $keyword){
				$ids[] = $keyword['Keyword']['modelId'];
				// 				if($key)
					// 					$title .= ' et';
					// 				$title .= __(' de %s', $keyword['Keyword']['name']);
			}
			$findversionOptions['joins'][] = array(
					'table' => 'RKKeywordForVersion',
					'alias' => 'KeywordForVersion',
					'type' => 'inner',
					'conditions' => array(
							'KeywordForVersion.versionId = version.modelId',
							'KeywordForVersion.keywordId' => $ids,
					));
		}
		if(isset($this->request->params['named']['keywordId'])){
	
			$keywordIds = $this->getAllSubKeywords($this->request->params['named']['keywordId']);
			//print_r($keywordIds); exit;
	
			// 			$title .= __(' pour le mot clé %s', $keywordIds[$this->request->params['named']['keywordId']]);
	
			$findversionOptions['joins'][] = array(
					'table' => 'RKKeywordForVersion',
					'alias' => 'KeywordForVersion',
					'type' => 'inner',
					'conditions' => array(
							'KeywordForVersion.versionId = version.modelId',
							'KeywordForVersion.keywordId' => array_keys($keywordIds),
					));
		}
		if(isset($this->request->params['named']['versionId'])){
			$findversionOptions['conditions']['Version.modelId'] = $this->request->params['named']['versionId'];
		}
	
		if(isset($this->request->params['named']['place'])){
			$locations = $this->getLocations($this->request->params['named']['place']);
			$ids = array();
			foreach ($locations as $key => $location){
				$ids[] = $location['PlaceName']['placeId'];
				/*if($key)
				 $title .= ' et';
					switch ($location['Place']['type']){
					case 1:
					$title .= __(' en %s', $location['PlaceName']['description']);
					break;
					case 2:
					$title .= __(' dans %s', $location['PlaceName']['description']);
					break;
					default:
					$title .= __(' à %s', $location['PlaceName']['description']);
					}*/
	
	
			}
			$findversionOptions['joins'][] = array(
					'table' => 'RKPlaceForVersion',
					'alias' => 'PlaceForVersion',
					'type' => 'inner',
					'conditions' => array(
							'PlaceForVersion.versionId = version.modelId',
							'PlaceForVersion.placeId' => $ids,
					));
		}
		else if(isset($this->request->params['named']['placeId'])){
	
			$places = $this->getLocations('', $this->request->params['named']['placeId']);
			$findversionOptions['joins'][] = array(
					'table' => 'RKPlaceForVersion',
					'alias' => 'PlaceForVersion',
					'type' => 'inner',
					'conditions' => array(
							'PlaceForVersion.versionId = version.modelId',
							'PlaceForVersion.placeId' => $this->request->params['named']['placeId'],
					));
		}
		if(isset($this->request->params['named']['from'])){
			$findversionOptions['conditions']['version.imageDate >='] = $this->Version->convertToAppleDate($this->request->params['named']['from']);
			// 			$title .= __(' à partir du %s', date("Y-m-d H:i:s", $this->request->params['named']['from']));
		}
	
		if(isset($this->request->params['named']['to'])){
			$findversionOptions['conditions']['version.imageDate <='] = $this->Version->convertToAppleDate($this->request->params['named']['to']);
			// 			$title .= __(' jusqu\'au %s', date("Y-m-d H:i:s", $this->request->params['named']['to']));
		}
		return $findversionOptions;
	
	}
	
	public function gallery() {
		return $this->redirect(
			array('controller' => 'galleries', 'action' => 'index', '?' => $this->request->query)
		);
	}
	
	
	private function getAlbum($uuid){
		$album = $this->Album->findByUuid($uuid);
		//todo not found
		return $album['Album'];
	}
	
	private function getLocations($locations, $placeId=null) {
		$findOptions = array(
// 				'order' => array('UPPER(description)'),
				'conditions' => array(
						//'PlaceName.language' => Configure::read('Config.language'),
						//'PlaceName.description' => $locations,
				),
				'fields' => array('DISTINCT PlaceName.placeId'),
		);
		if($placeId){
			$findOptions['conditions']['PlaceName.placeId'] = $placeId;
		}
		else{
			$findOptions['conditions']['PlaceName.description'] = $locations;
		}

// 		 		print_r($findOptions);
// 		 		exit;
		
		$placeName = $this->PlaceName->find('all', $findOptions);
		$ids = array();		
		foreach ($placeName as $place){
			$ids[] = $place['PlaceName']['placeId'];
		}
		
		
		$findOptions = array(
				'order' => array('UPPER(description)'),
				'contain' => array(
						'PlaceName' => array(
							'conditions' => array('PlaceName.language' => Configure::read('Config.language'),)
						)),
				'conditions' => array(
						'Place.modelid' => $ids,
				),
// 				'fields' => array('DISTINCT PlaceName.placeId'),
		);
		$placeName = $this->Place->find('all', $findOptions);
//  		print_r($placeName);
//  		exit;
		return $placeName;
	}
	

	
	private function getImageInfomation(&$versions){
		
		$versionIds = array();
		$properties = array();
		$this->Gallery->locale = Configure::read('Config.language');
		
		foreach ($versions as &$version){
			
			$versionIds[] = $version['Version']['modelId'];
			$properties[$version['Version']['modelId']] = array();


			$keywordIds = array();
			foreach ($version['Keyword'] as $keyword){
				$keywordIds[$keyword['modelId']] = $keyword['modelId'];
			}
			$keywordIds = $this->getAllParentsKeywords($keywordIds);
			$keywordUrls = array();
			foreach ($keywordIds as $keywordId){
					$keywordUrls[] = '/keywordId:'.$keywordId;
			}
			$this->Gallery->locale = Configure::read('Config.language');
			$version['keywords'] = $this->Gallery->find('list', array(
					'conditions' => array(
							'url' => $keywordUrls
					),
					'fields' => array('url', 'name')
			));			

			$placesUrls = array();
			foreach ($version['PlaceForVersion'] as &$place){
				$placesUrls[] = '/placeId:'.$place['placeId'];
			}
			$version['places'] = $this->Gallery->find('list', array(
					'conditions' => array(
							'url' => $placesUrls
					),
					'fields' => array('url', 'name'),
					'order' => 'name'
			));
			
			

			if($version['Version']['stackUuid']) {
				$version['Stack'] = $this->Version->findAllByStackuuid($version['Version']['stackUuid'], array('uuid', 'encodedUuid', 'name'));
			}			
			else{
				$version['Stack'] = array(
						array('Version' => array(
							//'uuid' => $version['Version']['uuid'],
							'name' => $version['Version']['name'],
							'encodedUuid'=> $version['Version']['encodedUuid'],
						))
				);
			}
			
		
		}
	
		
		$this->IptcProperty->contain('UniqueString');
		$iptcProperty = $this->IptcProperty->find('all', array(
				'conditions' => array(
						'versionid' => $versionIds,
						'propertyKey' => array('ObjectName', 'Byline'),
				)
		));
		
		foreach ($iptcProperty as &$prop){
			$properties[$prop['IptcProperty']['versionId']][$prop['IptcProperty']['propertyKey']] = $prop['UniqueString']['stringProperty'];
		}
		
		
		
		$this->OtherProperty->contain('UniqueString');
		$otherProperty = $this->OtherProperty->find('all', array(
				'conditions' => array(
						'versionid' => $versionIds,
						'propertyKey' => array('PixelSize', 'ProjectName'),
				)
		));

		foreach ($otherProperty as &$prop){
			$properties[$prop['OtherProperty']['versionId']][$prop['OtherProperty']['propertyKey']] = $prop['UniqueString']['stringProperty'];
		}
		
		$out = array(
				'properties' => &$properties
		);

		
		$this->set('properties', $properties);
		
		return $out;
	}
	
	
	
	public function image($encodedUuid) {
		$this->Version->contain(array('PlaceForVersion', 'Keyword'));
		$version = $this->Version->findByUuid(ApertureConnectorAppModel::decodeUuid($encodedUuid));
		$this->set('version', $version);

		$locations = array();
		foreach($version['PlaceForVersion'] as $location){
			$locations[] =  $location['placeId'];
		}
		
		
		$this->Place->contain(array('PlaceName' => array(
	        'conditions' => array('PlaceName.language =' => Configure::read('Config.language'))
	   	)));
		$places = $this->Place->findAllByModelid($locations, array('Place.defaultName'), 'type');
		$this->set('places', $places);
		
		
		$stack = array();
		if($version['Version']['stackUuid']) {
			$stack = $this->Version->findAllByStackuuid($version['Version']['stackUuid'], array('uuid', 'encodedUuid', 'name'));
		}
		$this->set('stack', $stack);
		
// 		print_r($version['Version']); 
// 		exit;
		
		$properties = array();
		$this->IptcProperty->contain('UniqueString');
		$properties['IptcProperty'] = $this->IptcProperty->findAllByVersionid($version['Version']['modelId']);
		
		$this->OtherProperty->contain('UniqueString');
		$properties['OtherProperty'] = $this->OtherProperty->findAllByVersionid($version['Version']['modelId']);
		
		$this->ExifStringProperty->contain('UniqueString');
		$properties['ExifStringProperty'] = $this->ExifStringProperty->findAllByVersionid($version['Version']['modelId']);
		
		//$this->ExifNumberProperty->contain('UniqueString');
		$properties['ExifNumberProperty'] = $this->ExifNumberProperty->findAllByVersionid($version['Version']['modelId']);
		
		$this->set('properties', $properties);
		
		
		
// 		$this->set('_serialize', array('version', 'stack', 'properties'));
		
	}
	
	public function places(){
		
		//get the count of photos per placeId
		$db = $this->Version->getDataSource();
		$result = $db->fetchAll(
				'SELECT "PlaceForVersion"."placeId" as placeid, count("PlaceForVersion"."versionId") as counter, replace(max("Version"."uuid"), \'%\', \'_\') as uuid
					FROM "RKVersion" AS "Version"
					LEFT JOIN "main"."RKPlaceForVersion" AS "PlaceForVersion" ON ("PlaceForVersion"."versionId" = "Version"."modelId")
					WHERE "Version"."isFlagged" = 1 AND "Version"."showInLibrary" = 1 AND "Version"."isHidden" = 0 AND "Version"."isInTrash" = 0 AND "Version"."mainRating" >= 0
					GROUP BY "PlaceForVersion"."placeId"'
		);
		
		$counter = array();
		$placeIds = array();
		foreach($result as $count){
			if($count[0]['placeid']){
				$counter[$count[0]['placeid']] = array(
						'counter' => $count[0]['counter'],
						'uuid' => $count[0]['uuid']
				);
				$placeIds[] = $count[0]['placeid'];
			}
		}
		
		$findOptions = array(
				'fields' => array(
						'substr(Place.centroid, 2, instr(Place.centroid, ", ")-2) as "Place.lng"',
						'substr(Place.centroid, instr(Place.centroid, ", ")+2, length(substr(Place.centroid, instr(Place.centroid, ", ")+2)-1)) as "Place.lat"',
						//  						'"PlaceName"."description" as "Marker.name"',
						'"Place"."defaultName"',
				'"Place"."type"'),/**/
				'contain' => array(
						'PlaceName' => array(
								'conditions' => array(
										'PlaceName.language' => Configure::read('Config.language'),
								),
								'fields' => array('"PlaceName"."description"'),
						)),
				'conditions' => array(
						//'substr(Place.centroid, instr(Place.centroid, \', \')+2, length(substr(Place.centroid, instr(Place.centroid, \', \')+2)-1)) BETWEEN ? AND ?' => array(48, 52),
						//'substr(Place.centroid, 2, instr(Place.centroid, \', \')-2) BETWEEN ? AND ?' => array(0, 10),
						'Place.type >=' => 2,
						'Place.modelId' => $placeIds
				),/**/
		);
		//45 quartiers
		//16 commune
		// 4 departement francais
		//2 region ou province
		//1 pays
		
		$places = $this->Place->find('all', $findOptions);
			
		//print_r($places);
		//exit;
		foreach ($places as $place){
			$modelId = $place['Place']['modelId'];
			$counter[$modelId]['type'] = $place['Place']['type'];
			$counter[$modelId]['zoom'] = 13;
			if($place['Place']['type'] <= 16)
				$counter[$modelId]['zoom'] = 10;
			if($place['Place']['type'] <= 2)
				$counter[$modelId]['zoom'] = 1;
			$counter[$modelId]['lat'] = $place['Place']['lat'];
			$counter[$modelId]['lng'] = $place['Place']['lng'];
			if(isset($place['PlaceName'][0])){
				$counter[$modelId]['name'] = $place['PlaceName'][0]['description'];
			}
			else{
				$counter[$modelId]['name'] = $place['Place']['defaultName'];
			}
			$counter[$modelId]['galleryUrl'] = Router::url(array(
					'controller' => 'aperture',
					'action' => 'gallery',
					'placeId' => $modelId,
					'language' => Configure::read('Config.language')
			));
			$counter[$modelId]['mapUrl'] = Router::url(array(
					'controller' => 'aperture',
					'action' => 'map',
					$modelId,
					'language' => Configure::read('Config.language')
			));
			$counter[$modelId]['html'] = HtmlHelper::tag('div', $counter[$modelId]['name']);
		
		}		
	}

	
	public function place($name) {

		$this->Place->contain(array('PlaceName' => array(
				'conditions' => array('PlaceName.language =' => Configure::read('Config.language'))
		)));
		$places = $this->Place->findAllByDefaultname($name);
		
// 		print_r($places);
// 		exit;
		
		$this->set('description', $places[0]['PlaceName'][0]['description']);
		

		$locations = '0';
		foreach ($places as $place){
			$locations .= ','.$place['Place']['modelId'];
		}
		
		
		
		$findversionOptions = array(
				'conditions' => array(
						'Version.isFlagged' => 1,
				),
				'joins' => array(
				    array('table' => 'RKPlaceForVersion',
				        'alias' => 'PlaceForVersion',
// 				        'type' => 'LEFT',
				        'conditions' => array(
				            'PlaceForVersion.versionId = version.modelId',
				        	'PlaceForVersion.placeId in ('.$locations.')'
				        )
				    )
				),
				//'fields' => array('Version.name', 'replace(Version.uuid, \'%\', \'_\') as "Version.encodedUuid"'),	
		//		'order' => 'ifnull(CustomSortOrder.orderNumber,999999999) '.($parent['Folder']['sortAscending'] == 1?'ASC':'DESC'),
		//		'fields' => array("Album.name", "Album.uuid", "Album.isMagic", "Album.albumType", "Album.albumSubclass", 'ifnull(CustomSortOrder.orderNumber,999999999) as "Album.orderNumber"', 'replace(Album.uuid, \'%\', \'_\') as "Album.encodedUuid"'),
		);	
		
		$versions = $this->Version->find('all',$findversionOptions);
		
		$this->set('versions', $versions);
		
		
	}	
	
	public function map($place = ''){

		//get info about the current location, belgium if missing
		$locations = $this->getLocations($place, isset($this->request->params['named']['placeId'])?$this->request->params['named']['placeId']:Configure::read('defaultMapPlaceId'));
		

		$this->set('location', $locations[0]);
				
	}
	
	public function markers(){		
		$markers = array();
		$console = array();
		
		
// 		//get the count of photos per placeId
// 		$db = $this->Version->getDataSource();
// 		$result = $db->fetchAll(
// 				'SELECT "PlaceForVersion"."placeId" as placeid, count("PlaceForVersion"."versionId") as counter, replace(max("Version"."uuid"), \'%\', \'_\') as uuid
// 					FROM "RKVersion" AS "Version"
// 					LEFT JOIN "main"."RKPlaceForVersion" AS "PlaceForVersion" ON ("PlaceForVersion"."versionId" = "Version"."modelId")
// 					WHERE "Version"."isFlagged" = 1 AND "Version"."showInLibrary" = 1 AND "Version"."isHidden" = 0 AND "Version"."isInTrash" = 0 AND "Version"."mainRating" >= 0
// 					GROUP BY "PlaceForVersion"."placeId"'
// 		);
		
// 		$counter = array();
// 		$placeIds = array();
// 		$galerieIconUuid = array();
// 		foreach($result as $count){
// 			if($count[0]['placeid']){
// 				$counter[$count[0]['placeid']] = array(
// 						'counter' => $count[0]['counter'],
// 						'uuid' => $count[0]['uuid']
// 				);
// 				$placeIds[] = $count[0]['placeid'];
// 			}
// 		}
		
		
		//$versions = array();		
		$findOptions = array(
// 				'order' => array('UPPER(description)'),
				'fields' => array(
						'substr(Place.centroid, 2, instr(Place.centroid, ", ")-2) as "Place.lng"', 
						'substr(Place.centroid, instr(Place.centroid, ", ")+2, length(substr(Place.centroid, instr(Place.centroid, ", ")+2)-1)) as "Place.lat"', 
//  						'"PlaceName"."description" as "Marker.name"', 
						'"Place"."defaultName"',
						'"Place"."type"',
						'modelId'),/**/
// 				'contain' => array(
// 						'PlaceName' => array(
// 							'conditions' => array(
// 									'PlaceName.language' => Configure::read('Config.language'),
// 							),
// 							'fields' => array('"PlaceName"."description"'),
// 				)),
				'conditions' => array(
					'CAST(substr(Place.centroid, instr(Place.centroid, \', \')+2, length(substr(Place.centroid, instr(Place.centroid, \', \')+2)-1)) as decimal) BETWEEN ? AND ?' => array((float)$this->request->query['south'], (float)$this->request->query['north']),
					'CAST(substr(Place.centroid, 2, instr(Place.centroid, \', \')-2) as decimal) BETWEEN ? AND ?' => array((float)$this->request->query['west'], (float)$this->request->query['east']),

// 					'Place.modelId' => $placeIds
				),

		);
		//45 quartiers
		//16 commune
		// 4 departement francais
		//2 region ou province
		//1 pays
		
		//$console[] = array((float)$this->request->query['west'], (float)$this->request->query['east']);
		//$console[] = array((float)$this->request->query['south'], (float)$this->request->query['north']);
		
		
		if($this->request->query['zoom'] > 11){ //quartier et commune
			$findOptions['conditions']['"Place"."type" >='] = 16;
		}
		else if($this->request->query['zoom'] > 9){ //commune
			$findOptions['conditions']['"Place"."type" BETWEEN ? AND ?'] = array(10, 20);
		}
		else if($this->request->query['zoom'] > 6){ //region/province
			$findOptions['conditions']['"Place"."type" BETWEEN ? AND ?'] = array(2, 10);
		}		
		else {//pays
			$findOptions['conditions']['"Place"."type" <='] = 1;
		}
		
		//print_r($findOptions);		
		$places = $this->Place->find('all', $findOptions);
//print_r($places);
//exit;

		
		$neighbors = array();
		foreach ($places as &$place){
			
			if($place['Place']['type'] >= 45){
				$neighbors[] = $place['Place']['modelId'];
			}
			/*if(isset($counter[$place['Place']['modelId']]) && $counter[$place['Place']['modelId']] > 0){
				
				$randRow = rand(0, $counter[$place['Place']['modelId']]['counter']-1);
				
			
				/*$result = $db->fetchAll(
					'SELECT replace("Version"."uuid", \'%\', \'_\') as uuid
					 FROM "RKVersion" AS "Version"
					 LEFT JOIN "main"."RKPlaceForVersion" AS "PlaceForVersion" ON ("PlaceForVersion"."versionId" = "Version"."modelId")
					 WHERE "Version"."isFlagged" = 1 AND "Version"."showInLibrary" = 1 AND "Version"."isHidden" = 0 AND "Version"."isInTrash" = 0 AND "Version"."mainRating" >= 0
					 AND "PlaceForVersion"."placeId" = '.$place['Place']['modelId'].'
					 ORDER BY RANDOM() LIMIT '.$randRow.', 1'
				);/** /
				
				//print_r($result);
				$markers[] = array(
						'lng' => $place['Place']['lng'],
						'lat' => $place['Place']['lat'],
						'type' => 'placeId',
						'id' => $place['Place']['modelId'],
						'counter' => $counter[$place['Place']['modelId']]['counter'],
						'label' => $place['Place']['defaultName'],
						//'uuid' =>  isset($result[0])?$result[0][0]['uuid']:$counter[$place['Place']['modelId']]['uuid'],
						'uuid' =>  $counter[$place['Place']['modelId']]['uuid'],
				);
			}*/
			$markers['/placeId:'.$place['Place']['modelId']] = array(
					'lng' => $place['Place']['lng'],
					'lat' => $place['Place']['lat'],
					'type' => 'placeId',
					'id' => $place['Place']['modelId'],
					'counter' => 0,
					//'label' => $place['Place']['defaultName'],
					//'uuid' =>  isset($result[0])?$result[0][0]['uuid']:$counter[$place['Place']['modelId']]['uuid'],
					//'uuid' =>  $counter[$place['Place']['modelId']]['uuid'],
			);
		}
		
		
		

		if($this->request->query['zoom'] > 13){
			
			$findversionOptions = array(
					'conditions' => array(
							'Version.isFlagged' => 1,
							'Version.showInLibrary' => 1,
							'Version.isHidden' => 0,
							'Version.isInTrash' => 0,
							'Version.mainRating >=' => 0,
							'Version.exifLatitude BETWEEN ? AND ?' => array($this->request->query['south'], $this->request->query['north']),
							'Version.exifLongitude BETWEEN ? AND ?' => array($this->request->query['west'], $this->request->query['east']),
					),
					'fields' => array('Version.modelId', 'Version.encodedUuid', 'Version.encodedUuid', 'Version.name', 'Version.exifLatitude', 'Version.exifLongitude'),
			);
			
			if(count($neighbors) > 0){
				$findversionOptions['joins'][] = array(
						'table' => 'RKPlaceForVersion',
						'alias' => 'PlaceForVersion',
						'type' => 'left',
						'conditions' => array(
								'PlaceForVersion.versionId = version.modelId',
								'PlaceForVersion.placeId' => $neighbors,
						));
				$findversionOptions['fields'][] = 'PlaceForVersion.placeId';
				
			}
			
			
			$versions = $this->Version->find('all',$findversionOptions);
			
			foreach($versions as &$version){
				
				/*if(!isset($version['PlaceForVersion']) || !$version['PlaceForVersion']['placeId']){//si pas associe a un quartier
					$findObjectNameOptions = array(
							'contain' => 'UniqueString',
							'conditions' => array(
									'IptcProperty.versionId' => $version['Version']['modelId'],
									'IptcProperty.propertyKey' => 'ObjectName',
							),
							'fields' => array('UniqueString.stringProperty'),
					);
					
					$IptcProperty = $this->IptcProperty->find('all', $findObjectNameOptions);
					
					$label = $version['Version']['name'];
				
					if(count($IptcProperty) > 0){
						$label = $IptcProperty[0]['UniqueString']['stringProperty'];
					}
					
					$markers[] = array(
								'lng' => $version['Version']['exifLongitude'],
								'lat' => $version['Version']['exifLatitude'],
								'type' => 'versionId',
							'id' => $version['Version']['modelId'],
							'counter' => 1,
							'label' => $label,
							'uuid' =>  $version['Version']['encodedUuid'],
					);
					
				}/**/		
				$markers['/versionId:'.$version['Version']['modelId']] = array(
						'lng' => $version['Version']['exifLongitude'],
						'lat' => $version['Version']['exifLatitude'],
						'type' => 'versionId',
						'id' => $version['Version']['modelId'],
						'counter' => 1,
						//'label' => $label,
						'uuid' =>  $version['Version']['encodedUuid'],
				);
				
			}

		}

		$this->Gallery->locale = Configure::read('Config.language');
		$galleries = $this->Gallery->find('all', array(
					'conditions' => array('url' => array_keys($markers), 'published' => true)
			));
		
		foreach ($galleries as &$gallery){
			if(isset($markers[$gallery['Gallery']['url']])){
				$markers[$gallery['Gallery']['url']]['counter'] = $gallery['Gallery']['counter'];
				$markers[$gallery['Gallery']['url']]['uuid'] = $gallery['Gallery']['thumbUuid'];
				$markers[$gallery['Gallery']['url']]['label'] = $gallery['Gallery']['name'];
			}
		}
		
// 		print_r($markers); exit;
		
		$this->set('markers', $markers);
		$this->set('console', $console);
	}
	
	public function price($encodedUuid) {

		$this->Version->contain(array('PlaceForVersion', 'Keyword'));
		$version = $this->Version->findByUuid(ApertureConnectorAppModel::decodeUuid($encodedUuid));
		
		$versions = array($version);
		
		$this->getImageInfomation($versions);
		
		$this->set('version', $versions[0]);
	}
	
}?>