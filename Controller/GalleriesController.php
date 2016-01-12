<?php
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

App::uses('AppController', 'Controller');
/**
 * Galleries Controller
 *
 * @property Gallery $Gallery
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 * @property AclComponent $Acl
 */
class GalleriesController extends AppController {

	public $uses = array('Gallery');
	public $components = array('RequestHandler', 'Paginator', 'Acl', 'Session');
	private $client;
	
	const PAGING_SIZE = 50;
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('index', 'search', 'keyword', 'view', 'place', 'geohash');
		$this->client = ClientBuilder::create()           // Instantiate a new ClientBuilder
			->setHosts(Configure::read('awsESHosts'))      // Set the hosts
			->build();              // Build the client object
	}
	
	private function execute($body){

		
// 		echo $body; exit;
		
		$searchParams = [
			'index' => Configure::read('Config.apertureIndex'),
			'type'  => Configure::read('Config.versionModel'),
			'body' => $body,
		];
		
		
		$retDoc = $this->client->search($searchParams);
		if($retDoc['hits']['total'] > 0){			
			$this->view = 'view';
			$this->set('versions', $retDoc);
			$this->set('jsIncludes', ['bower/jail/dist/jail.min', 'gallery2']);
		}
		else {
			$this->view = 'empty';
		}
		return $retDoc;
	}
	
	
	public function place($locationUuid) {

		$this->fixUuid($locationUuid);
		$body = json_decode('
			{
			   "size": '.json_encode(self::PAGING_SIZE).',
			   "query": {
			      "function_score": {
			         "query": {
			            "bool": {
			               "must": [
			                  {
			                     "nested": {
			                        "path": "locations",
			                        "query": {
			                           "term": {
			                              "locations.uuid": {
			                                 "value": '.json_encode($locationUuid).'
			                              }
			                           }
			                        }
			                     }
			                  }
			               ]
			            }
			         },
			         "field_value_factor": {
			            "field": "rating"
			         }
			      }
			   }
			}
		');
		
		$retDoc = $this->execute($body);
		$this->set('title', __('Galerie pour la localisation "%s"', $locationUuid));
		foreach ($retDoc["hits"]["hits"][0]["_source"]["locations"] as $location){
			if(strcmp($location['uuid'], $locationUuid) == 0){				
				$this->set('title', __('Galerie pour la localisation "%s"', $location['name_'.Configure::read('Config.language')]));
				break;
			}
		}
	}
	
	
	
	public function geohash($hash) {
		$this->fixUuid($locationUuid);
		$body = json_decode('
			{
			   "size": '.json_encode(self::PAGING_SIZE).',
			   "query": {
			      "function_score": {
			         "query": {
			            "match_all": {}
			         },
			         "filter": {
			            "geohash_cell": {
			               "location": '.json_encode($hash).'
			            }
			         },
			         "field_value_factor": {
			            "field": "rating"
			         }
			      }
			   }
			}
		');
		
		$retDoc = $this->execute($body);
		
		$elemTitle = isset($retDoc["hits"]["hits"][0]["_source"]["label"])?$retDoc["hits"]["hits"][0]["_source"]["label"]:$retDoc["hits"]["hits"][0]["_source"]["name"];
		
		if($retDoc["hits"]["total"] > 1){
			$this->set('title', __('Galerie pour "%s" et aux alentours', $elemTitle));
		}
		else {
			$this->set('title', __('Galerie pour la serie "%s"', $elemTitle));
		}
	}
	
	
	public function keyword($keywordName) {		
		
		$body = json_decode('
		{
		  "size": '.json_encode(self::PAGING_SIZE).',
		  "query": {
		    "function_score": {
		      "query": {
		        "bool": {
		          "must": [
		            {
		              "nested": {
		                "path": "Keywords",
		                "query": {
		                  "bool": {
		                    "must": [
		                      {
		                        "match": {
		                          "Keywords.name_'.Configure::read('Config.language').'": {      
		                "query":    '. json_encode($keywordName) . ',
		                "operator": "and" }
		                        }
		                      }
		                    ]
		                  }
		                }
		              }
		            }
		          ]
		        }
		      },
		      "field_value_factor": {
		        "field": "rating"
		      }
		    }
		  }
		}');
	
		$this->set('title', __('Galerie pour le mot-clé "%s"', $keywordName));
		$this->execute($body);

	}	
	
	
	public function view ($slug = NULL) {

		var_dump($slug);
		$result = $this->Gallery->findBySlug($slug);
		var_dump($result);
// 		exit;
	}


	
// 	public function library() {
// 		$model = $this->{$this->modelClass};
// 		//print_r($model); exit;
// 		$model->locale = Configure::read('Config.language');
// 		$model->recursive = 0;
// 		/*$model->conditions = array(
// 				'keyword' => true,
// 				'publish' => true,
// 				'keyword' => true
// 		);*/
// 		//$model->limit = false;
// 		//$model->order = 'Gallery__i18n_nameTranslation';
// 		$this->set($model->useTable, $model->find('all', array(
// 				'order' => 'Gallery__i18n_nameTranslation',
// 				'conditions' => array(
// 					'keyword' => true,
// 					'published' => true,
// 					'keyword' => true
// 				),
// 				''
// 		)));	
// 	}
	
	
// 	public function keywords(){
		

// 		$searchQuery['search_type'] = 'count';
// 		$searchQuery['body'] = json_decode('
// 		{
// 		  "aggs": {
// 		    "keywords": {
// 		      "nested": {
// 		        "path": "Keywords"
// 		      },
// 		      "aggs": {
// 		        "keyword_name": {
// 		          "terms": {
// 		            "size": 0,
// 		            "field": "Keywords.name_'.Configure::read('Config.language').'.raw",
// 		            "order": {
// 		              "_term": "asc"
// 		            }
// 		          },
// 		          "aggs": {
// 		            "keyword_id": {
// 		              "terms": {
// 		                "size": 0,
// 		                "field": "Keywords.id"
// 		              }
// 		            }
// 		          }
// 		        }
// 		      }
// 		    }
// 		  }
// 		}');
		
// 		$client = new Client();

// 		$retDoc = $client->search($searchQuery);
		
// 		$this->set('keywords', $retDoc['aggregations']['keywords']);
// 	}
	
	
// 	public function index($query) {
	
// 		$client = new Client();
	
// 		$searchParams = array(
// 				'index' => 'index',
// 				'type'  => 'version'
	
// 		);
	
// 		// 		if(isset($this->request->query['q'])){
// 		// 			$searchParams['body']['query']['match']['_all'] = $this->request->query['q'];
// 		// 		}
	
// 		$retDoc = $client->search($searchParams);
	
// 		$this->set('title', __('Galerie photo'));
	
// 		// 		var_dump($retDoc);
// 		// 		exit;
	
// 		$this->set('versions', $retDoc);
	
	

// 			$this->set('jsIncludes', ['bower/jail/dist/jail.min', 'gallery2']);
// 			$this->view = 'view';
	
// 			//$this->set('_serialize', array('properties', 'places', 'versions'));
	
// 	}
	
	
	public function search($query) {
		
		if(isset($this->request->query['q'])){
			$query = $this->request->query['q'];	
			$this->redirect([
					$query
			]);
		}
		else{
			$this->set('title', __('Résultat de la recherche pour "%s"', $query));
			$this->execute('
				{
			      "size": '.json_encode(self::PAGING_SIZE).',
				  "query": {
				    "function_score": {
				      "query": {
				        "bool": {
				          "should": [
				            {
				              "match": {
				                "label": {
				                  "query": '.json_encode($query).',
				                  "operator": "and"
				                }
				              }
				            },
				            {
				              "nested": {
				                "path": "Keywords",
				                "query": {
				                  "bool": {
				                    "must": [
				                      {
				                        "match": {
				                          "Keywords.name_'.Configure::read('Config.language').'": {
				                            "query": '.json_encode($query).',
				                            "operator": "and"
				                          }
				                        }
				                      }
				                    ]
				                  }
				                }
				              }
				            },
				            {
				              "nested": {
				                "path": "locations",
				                "query": {
				                  "bool": {
				                    "must": [
				                      {
				                        "match": {
				                          "locations.name_'.Configure::read('Config.language').'": {
				                            "query": '.json_encode($query).',
				                            "operator": "and"
				                          }
				                        }
				                      }
				                    ]
				                  }
				                }
				              }
				            }
				          ]
				        }
				      },
				      "field_value_factor": {
				        "field": "rating"
				      }
				    }
				  }
				}					
					');
		}

	}
	

	
	

	public function admin () {
		$this->Gallery->locale = DEFAULT_LANGUAGE;
	
		$this->set('galleries', $this->Gallery->find('all'));
	}
	
	
}
