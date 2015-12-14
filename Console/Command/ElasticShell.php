<?php

use Elasticsearch\Client;
// Can be called by "Console/cake index"

App::uses('Shell', 'Console');

App::import('Controller', 'Aperture');

class ElasticShell extends AppShell {
	
	private $properties = [
			'Byline',
			'CopyrightNotice',
			'CiEmailWork',
			'UsageTerms',
			'City',
			'SubLocation',
			'ObjectName',
			'MasterLocation',
			'AspectRatio',
			'PixelSize',
			'ProjectName',
			'LensModel',
			'Model',
			'Copyright',
			'Artist',
			'UserComment',
			'Altitude',
			'Longitude',
			'Latitude'
	];
	
	private $client;

	public function initialize()
	{
		parent::initialize();
		$this->loadModel('Gallery');
		$this->loadModel('ApertureConnector.Place');
		$this->loadModel('ApertureConnector.Version');
		$this->loadModel('ApertureConnector.IptcProperty');
		$this->loadModel('ApertureConnector.OtherProperty');
		$this->loadModel('ApertureConnector.ExifStringProperty');
		$this->loadModel('ApertureConnector.ExifNumberProperty');
		$this->loadModel('ApertureConnector.ImageProxyState');
	
	}	
	public function updateVersionsInfo(){
		$this->out('Indexing versions...');
		$aperture = new ApertureController();
		$aperture->constructClasses(); //I needed this in here for more complicated requiring component loads etc in the Controller
	
		$findversionOptions = $aperture->buildFindversionOptions();
	
		$versions = $this->Version->find('all', $findversionOptions);
		foreach ($versions as $version){

			$data['uuid'] = $version['Version']['encodedUuid'];
			$data['name'] = $version['Version']['name'];
			$data['date'] = $version['Version']['unixImageDate'];
			$data['width'] = $version['Version']['masterWidth'];
			$data['height'] = $version['Version']['masterHeight'];
			$data['location'] = array(
					'lat' => $version['Version']['exifLatitude'],
					'lon' => $version['Version']['exifLongitude']
			);
			$data['server'] = 'http://global.theus.be/';
			
			//$data['url'] = Configure::read('repositoryId');
			$imageProxy = $this->ImageProxyState->findByVersionuuid($version['Version']['uuid']);
			$data['path'] = $imageProxy['ImageProxyState']['thumbnailPath'];
				
			$data['Locations'] = array();
			foreach($version['PlaceForVersion'] as $location){
				$data['Locations'][] =  $location['placeId'];
			}
				
	
			$data['Keywords'] = array();
			foreach($version['Keyword'] as $keyword){
				$data['Keywords'][] =  $keyword['modelId'];
			}
				
			$data['Stack'] = array();
			if($version['Version']['stackUuid']) {
				$stackVersions = $this->Version->findAllByStackuuid($version['Version']['stackUuid'], array('uuid', 'encodedUuid', 'name', 'unixImageDate'), array('unixImageDate'));
				foreach($stackVersions as $stackVersion){
					//if(strcmp($data['uuid'], $stackVersion['Version']['uuid']) != 0){
						$imageProxy = $this->ImageProxyState->findByVersionuuid($stackVersion['Version']['uuid']);
						//$data['path'] = $imageProxy['ImageProxyState']['thumbnailPath'];
						$data['Stack'][] = array(
							'path' => $imageProxy['ImageProxyState']['thumbnailPath'],
							'uuid' => $stackVersion['Version']['encodedUuid']						
						);
					//}
				}
			}
	
			$data['Properties'] = array();
				
			$this->IptcProperty->contain('UniqueString');
			$props = $this->IptcProperty->findAllByVersionid($version['Version']['modelId']);
			foreach($props as $prop){
				if(in_array($prop['IptcProperty']['propertyKey'], $this->properties))
					$data['Properties'][$prop['IptcProperty']['propertyKey']] = $prop['UniqueString']['stringProperty'];
			}
				
			$this->OtherProperty->contain('UniqueString');
			$props = $this->OtherProperty->findAllByVersionid($version['Version']['modelId']);
			foreach($props as $prop){
				if(in_array($prop['OtherProperty']['propertyKey'], $this->properties))
					$data['Properties'][$prop['OtherProperty']['propertyKey']] = $prop['UniqueString']['stringProperty'];
			}
				
			$this->ExifStringProperty->contain('UniqueString');
			$props = $this->ExifStringProperty->findAllByVersionid($version['Version']['modelId']);
			foreach($props as $prop){
				if(in_array($prop['ExifStringProperty']['propertyKey'], $this->properties))
					$data['Properties'][$prop['ExifStringProperty']['propertyKey']] = $prop['UniqueString']['stringProperty'];
			}
				
			$props = $this->ExifNumberProperty->findAllByVersionid($version['Version']['modelId']);
			foreach($props as $prop){
				if(in_array($prop['ExifNumberProperty']['propertyKey'], $this->properties))
					$data['Properties'][$prop['ExifNumberProperty']['propertyKey']] = $prop['ExifNumberProperty']['numberProperty'];
			}
				
			//json_encode($data);
	
			$this->putObjectToElasticSearch($data, "version", $version['Version']['encodedUuid']);
				
			//$this->out($data);
			//break;
		}
	
	
	
	}
	

	public function putObjectToElasticSearch($data, $type, $id){
		
// 		$client = new Client();
		
		$params = [
				'index' => 'index',
				'type' => 'version',
				'id' => $id,
				'body' => $data
		];
		
		//dump
		// Document will be indexed to my_index/my_type/my_id
		$response = $this->client->index($params);
		
		//var_dump($data);
		
		
		
		
		
		/*$searchParams['index'] = 'global';
		$searchParams['type']  = 'version';*/
		
		
		
		/*$data_json = json_encode($data);
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, Configure::read('elasticSearchUrl').$type."/".$id);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_json)));
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response  = curl_exec($ch);
		$this->out($response);
		curl_close($ch);*/
	}	
	
	
	public function main() {
		$this->client = new Client();
		
		$params = ['index' => 'index'];
		$response = $this->client->indices()->delete($params);
		
		
		
		$this->out('Start indexing series');
		$this->updateVersionsInfo();
	}
}