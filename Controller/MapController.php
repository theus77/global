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
		$client = ClientBuilder::create()           // Instantiate a new ClientBuilder
			->setHosts(Configure::read('awsESHosts'))      // Set the hosts
			->build();              // Build the client object


		$bb = [
				"top_left" => [
						"lat" => (float)$this->request->query['north'],
						"lon" => (float)$this->request->query['west']
				],
				"bottom_right" => [
						"lat" => (float)$this->request->query['south'],
						"lon" => (float)$this->request->query['east']
				]
		];

		$getImages = false;
		if($this->request->query['zoom'] > 11){ //quartier et commune
			$levelRange = [ 'gte' => 16 ];

			if($this->request->query['zoom'] >= 14){
				$getImages = 6;
			}
		}
		else if($this->request->query['zoom'] > 9){ //commune
			$levelRange = [
					'gte' => 16,
					'lte' => 20,

			];
		}
		else if($this->request->query['zoom'] > 6){ //region/province
			$levelRange = [
					'gte' => 2,
					'lte' => 14,

			];
		}
		else {//pays
			$levelRange = [
					'lte' => 1,

			];
		}

		if($getImages){
			$aggImageQuery = '
					{
					    "size": 0,
					    "aggregations" : {
					        "zoomedInView" : {
					            "filter" : {
					                "geo_bounding_box" : {
					                    "location" : '.json_encode($bb).'
					                }
					            },
					            "aggregations":{
					                "zoom1":{
					                    "geohash_grid" : {
					                        "field":"location",
					                        "precision": '.json_encode($getImages).'
					                    },
					                "aggregations" : {
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
								                                "uuid",
									                              "location.lon",
									                              "location.lat",
																								"label",
																								"name"
								                              ]
								                            },
								                            "size": 1
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
					'body' => $aggImageQuery
			];

			// 		echo json_encode($searchQuery['body']); exit;
			// echo $aggImageQuery; exit;
			$aggr = $client->search($searchQuery);

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
			                  "locations.location": '.json_encode($bb).'
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
			                            "size": 1
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

		// 		echo json_encode($searchQuery['body']); exit;

		$retDoc = $client->search($searchQuery);


		$this->set('markers', $retDoc['aggregations']['locations']);
		$this->set('aggregated', isset($aggr)?$aggr['aggregations']['zoomedInView']:[]);


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
