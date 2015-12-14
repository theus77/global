<?php
use Elasticsearch\Client;

App::uses('ImgController', 'ApertureConnector.Controller');


class ImageController extends ImgController {
	
	
	
	public function viewVersion($uuid) {
		
		$client = new Client();
		$indexParams['index']  = 'index';

		if($client->indices()->exists($indexParams)) {
			$search = $client->search([
					'index' => 'index',
					'type' => 'version',
				    'body' => [
				        'query' => [
				            'match' => [
				                'Stack.uuid' => $uuid
				            ]
				        ]
				    ]
			]);
			
// 			var_dump($search) ;
// 			exit;
			
			if( $search && $search['hits']["total"] > 0 ) {
				
				$version = $search['hits']["hits"][0];
				
// 				echo Router::url('/', true)."<br>";
// 				echo $version['_source']['server'];
// 				exit;

				if(strcmp($version['_source']['server'], Router::url('/', true)) != 0) {
				
				
				//strcmp(Router::url('/', true), $this->params->url) != 0
// 				var_dump($version) ;
// 				exit;
				//echo Router::url('/', true);
				//var_dump( $this->params).'<br>';
// 				echo $version['_source']['server'].$this->params->url;
// 				exit;
					$this->redirect($version['_source']['server'].$this->params->url);
				}
			}
		}/**/
		//parent::viewVersion($uuid);
	}
}