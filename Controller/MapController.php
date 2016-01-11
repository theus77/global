<?php
use Elasticsearch\ClientBuilder;
class MapController extends AppController {

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('index', 'markers');
		$this->client = ClientBuilder::create()           // Instantiate a new ClientBuilder
			->setHosts(Configure::read('awsESHosts'))      // Set the hosts
			->build();              // Build the client object
	}
	
	

	public function markers(){
		
		
		if($this->request->query['zoom'] > 11){ //quartier et commune
			$levelRange = [ 'gte' => 16 ];
		}
		else if($this->request->query['zoom'] > 9){ //commune
			$levelRange = [ 
					'gte' => 10,
					'lte' => 20,
						
			];
		}
		else if($this->request->query['zoom'] > 6){ //region/province
			$levelRange = [ 
					'gte' => 2,
					'lte' => 10,
						
			];
		}
		else {//pays
			$levelRange = [ 
					'lte' => 1,
						
			];
		}
		
		$query = '
			{
			  "size": 0,
			  "aggs": {
			    "locations": {
			      "nested": {
			        "path": "locations"
			      },
			      "aggs": {
			        "by_level": {
			          "filter": {
			            "range": {
			              "locations.level": '.json_encode($levelRange).'
			            }
			          },
			          "aggs": {
			            "by_coordinate": {
			              "filter": {
			                "geo_bounding_box": {
			                  "locations.location": {
			                    "top_left": {
			                      "lat": '.json_encode((float)$this->request->query['north']).',
			                      "lon": '.json_encode((float)$this->request->query['west']).'
			                    },
			                    "bottom_right": {
			                      "lat": '.json_encode((float)$this->request->query['south']).',
			                      "lon": '.json_encode((float)$this->request->query['east']).'
			                    }
			                  }
			                }
			              },
			              "aggs": {
			                "by_location": {
			                  "terms": {
			                    "field": "locations.uuid",
			                    "size": 200
			                  },
			                  "aggs": {
			                    "top_version": {
			                      "top_hits": {
			                        "size": 1
			                      }
			                    },
			                    "location_version": {
			                      "reverse_nested": {
			                        "path": ""
			                      },
			                      "aggs": {
			                        "icon": {
			                          "top_hits": {
			                            "sort": [
			                              {
			                                "rating": {
			                                  "order": "desc"
			                                }
			                              },
			                              {
			                                "lastUpdateDate": {
			                                  "order": "desc"
			                                }
			                              }
			                            ],
			                            "_source": {
			                              "include": [
			                                "uuid"
			                              ]
			                            },
			                            "size": 5
			                          }
			                        }
			                      }
			                    }
			                  }
			                }
			              }
			            }
			          }
			        }
			      }
			    }
			  }
			}				
			';
		
		$searchQuery = [
			'index' => 'aperture',
			'type' => 'version',
			'body' => $query	
		];
		
		
		$client = ClientBuilder::create()           // Instantiate a new ClientBuilder
		->setHosts(Configure::read('awsESHosts'))      // Set the hosts
		->build();              // Build the client object
		
		// 		echo json_encode($searchQuery['body']); exit;
			
		$retDoc = $client->search($searchQuery);
		
		
		$this->set('markers', $retDoc['aggregations']['locations']);
			
		
	}
	
	public function index() {
// 		$searchParams = [
// 			'index' => Configure::read('Config.apertureIndex'),
// 			'type'  => Configure::read('Config.placeModel'),
// 			'id' => $placeUuid,
// 		];
	
// 		$retDoc = $this->client->get($searchParams);
		
// 		var_dump($retDoc); exit;
	}
	
}