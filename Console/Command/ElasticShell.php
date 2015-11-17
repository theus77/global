<?php

// Can be called by "Console/cake index"

App::uses('Shell', 'Console');

App::import('Controller', 'Aperture');

class ElasticShell extends AppShell {

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
	
	}	
	public function updateVersionsInfo(){
		$this->out('Indexing versions...');
		$aperture = new ApertureController();
		$aperture->constructClasses(); //I needed this in here for more complicated requiring component loads etc in the Controller
	
		$findversionOptions = $aperture->buildFindversionOptions();
	
		$versions = $this->Version->find('all', $findversionOptions);
		foreach ($versions as $version){
				
			$data = $version['Version'];
			$data['RepositoryId'] = Configure::read('repositoryId');
				
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
					$data['Stack'][] = $stackVersion['Version'];
				}
			}
	
			$data['Properties'] = array();
				
			$this->IptcProperty->contain('UniqueString');
			$props = $this->IptcProperty->findAllByVersionid($version['Version']['modelId']);
			foreach($props as $prop){
				$data['Properties'][$prop['IptcProperty']['propertyKey']] = $prop['UniqueString']['stringProperty'];
			}
				
			$this->OtherProperty->contain('UniqueString');
			$props = $this->OtherProperty->findAllByVersionid($version['Version']['modelId']);
			foreach($props as $prop){
				$data['Properties'][$prop['OtherProperty']['propertyKey']] = $prop['UniqueString']['stringProperty'];
			}
				
			$this->ExifStringProperty->contain('UniqueString');
			$props = $this->ExifStringProperty->findAllByVersionid($version['Version']['modelId']);
			foreach($props as $prop){
				$data['Properties'][$prop['ExifStringProperty']['propertyKey']] = $prop['UniqueString']['stringProperty'];
			}
				
			$props = $this->ExifNumberProperty->findAllByVersionid($version['Version']['modelId']);
			foreach($props as $prop){
				$data['Properties'][$prop['ExifNumberProperty']['propertyKey']] = $prop['ExifNumberProperty']['numberProperty'];
			}
				
			//json_encode($data);
	
			$this->putObjectToElasticSearch($data, "version", $version['Version']['encodedUuid']);
				
			//$this->out($data);
			break;
		}
	
	
	
	}
	

	public function putObjectToElasticSearch($data, $type, $id){
		$data_json = json_encode($data);
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, Configure::read('elasticSearchUrl').$type."/".$id);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_json)));
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response  = curl_exec($ch);
		$this->out($response);
		curl_close($ch);
	}	
	
	
	public function main() {
		$this->out('Start indexing series');
		$this->updateVersionsInfo();
	}
}