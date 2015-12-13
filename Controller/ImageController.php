<?php
use Elasticsearch\Client;

App::uses('ImgController', 'ApertureConnector.Controller');


class ImageController extends ImgController {
	
	
	
	public function viewVersion($uuid) {
		
		/*$client = new Client();
		$indexParams['index']  = 'index';

		if($client->indices()->exists($indexParams)) {
			$version = $client->get([
					'index' => 'index',
					'type' => 'version',
					'id' => $uuid
			]);
			
			if($version && $version['found'] && strcmp(Router::url('/', true), $this->params->url) != 0) {
				//var_dump($version) ;
				//echo Router::url('/', true);
				//var_dump( $this->params).'<br>';
				//echo $version['_source']['server'];
				$this->redirect($version['_source']['server'].$this->params->url);
			}
		}/**/
		parent::viewVersion($uuid);
	}
}