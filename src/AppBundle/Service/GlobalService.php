<?php
namespace AppBundle\Service;

use Elasticsearch\Client;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GlobalService {
	
	/** @var Client $client*/
	private $client;
	private $prefix;
	private $environment;
	private $apertureIndex;
	
	public function __construct(Client $client, $prefix, $environment, $apertureIndex){
		$this->client = $client;
		$this->environment = $environment;
		$this->prefix = $prefix;
		$this->apertureIndex = $apertureIndex;
	}
	
	public function getKeywords($language){
		$searchQuery = [
			'index' => $this->apertureIndex,
			'type' => 'version',
			'search_type' => 'count'];
		
		$searchQuery['body'] = ('
			{
			   "size": 0,
			   "aggs": {
			      "keywords": {
			         "nested": {
			            "path": "Keywords"
			         },
			         "aggs": {
			            "keyword_name": {
			               "terms": {
			                  "field": "Keywords.name_'.$language.'.raw",
			                  "order": {
			                     "_term": "asc"
			                  },
			                  "size": 10000
			               },
			               "aggs": {
			                  "by_uuid": {
			                     "terms": {
			                        "field": "Keywords.uuid"
			                     },
			                     "aggs": {
			                        "keyword_to_version": {
			                           "reverse_nested": {},
			                           "aggs": {
			                              "top_uuid_hits": {
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
			}');
		
// 		dump($searchQuery['body']);
			
		$retDoc = $this->client->search($searchQuery);

		return $retDoc['aggregations']['keywords'];
	}
	
	
	public function getStats() {
		$searchParams = [
				'index' => $this->apertureIndex,
				'type'  => 'version',
				'body' => '
				{
				   "size": 0,
				   "aggs": {
				     "counter": {
				       "cardinality": {
				         "field": "Stack"
				       }
				     }
				   }
				}
			',
		];
		
		
		$retDoc = $this->client->search($searchParams);
		return $retDoc;
	}
	
	public function getMarkers($config) {
		
		$bb = [
				"top_left" => [
						"lat" => (float)$config['north'],
						"lon" => (float)$config['west']
				],
				"bottom_right" => [
						"lat" => (float)$config['south'],
						"lon" => (float)$config['east']
				]
		];
		
		$getImages = false;
		if($config['zoom'] > 11){ //quartier et commune
			$levelRange = [ 'gte' => 16 ];
		
			if($config['zoom'] >= 14){
				$getImages = 8;
			}
		}
		else if($config['zoom'] > 9){ //commune
			$levelRange = [
					'gte' => 16,
					'lte' => 20,
		
			];
		}
		else if($config['zoom'] > 6){ //region/province
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
					'index' => $this->apertureIndex,
					'type' => 'version',
					'body' => $aggImageQuery
			];
		
// 					echo json_encode($searchQuery['body']); exit;
			// echo $aggImageQuery; exit;
			$aggr = $this->client->search($searchQuery);

// 			dump($aggr);
		
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
		
		
// 		echo($query); exit;
		$searchQuery = [
				'index' => $this->apertureIndex,
				'type' => 'version',
				'body' => $query
		];
		
		
		$retDoc = $this->client->search($searchQuery);
		
// 		dump($retDoc);

		$out= [
			'markers' => $retDoc['aggregations']['locations'],
			'aggregated' => isset($aggr)?$aggr['aggregations']['zoomedInView']:[]
		];
		return $out;
	}
	
	public function nextFlights() {
		$param = [
				'from' => 0,
				'size' => 3,
				'index' => $this->prefix.$this->environment,
				'type' => 'flight',
				'body' => [
						'sort' => [
								'order' => [
									'order' => 'asc',
									'missing' => '_last',
								]
						]
				],
		];
		
		return $this->client->search($param);
	}
	
	public function getFlight($ouuid){
		try {
			return $this->client->get([
					'index' => $this->prefix.$this->environment,
					'type' => 'flight',
					'id' => $ouuid,
			]);
		}
		catch (\Exception $e){
			throw new NotFoundHttpException('Flight not found');
		}
	}
	
	public function getPlace($ouuid){
		try {
			return $this->client->get([
					'index' => $this->apertureIndex,
					'type' => 'place',
					'id' => $ouuid,
			]);
		}
		catch (\Exception $e){
			throw new NotFoundHttpException('Flight not found');
		}
	}

	public function getGalleriesOnTop() {
		$param = [
				'from' => 0,
				'size' => 10,
				'index' => $this->prefix.$this->environment,
				'type' => 'gallery',
				'body' => '{"query":{"and":[{"term":{"homepage":{"value":"1","boost":1}}}]}}',
		];
		$result = $this->client->search($param);
		
		foreach ($result['hits']['hits'] as $index => &$item){
			try {
				$param = [
						'size' => 0,
						'index' => $this->apertureIndex,
						'type' => 'version',
						'body' => $item['_source']['query'],
				];
				$subresult = $this->client->search($param);
				$item['_counter'] = $subresult['hits']['total'];				
			}
			catch(\Exception $e){
				unset($result['hits']['hits'][$index]);
			}
			
		}
		
		return $result;		
	}
	


	public function getImageInfo($ouuid) {
		return $this->client->get([
				'index' => $this->apertureIndex,
				'type' => 'version',
				'id' => $ouuid ]);
	}
	
	public function getTexts($page) {
		$param = [
				'from' => 0,
				'size' => 100,
				'index' => $this->prefix.$this->environment,
				'type' => 'texte',
				'body' => [
						'query' => [
								'term' => [
									'page' => $page,
								]
						]
				],
		];
		
		$result = $this->client->search($param);
		$out = [];
		foreach ($result['hits']['hits'] as $item){
			$out[$item['_source']['key']] = $item['_source'];
		}
		
		return $out;
	}
	
}