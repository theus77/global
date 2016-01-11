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
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('index', 'search', 'keyword', 'view', 'place');
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
	}
	
	
	public function keyword($keywordName) {		
		
		$body = json_decode('
			{
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
		
		
// 		$searchParams = [
// 				'index' => 'aperture',
// 				'type'  => 'keyword',
// 				'body' => json_decode('{"query":{ "ids":{ "values": [ '.$keywordid.' ] } } }'),
// 		];		
		

// 		$retDoc = $this->client->search($searchParams);
		
// 		var_dump($retDoc); exit;
		
// 		if($retDoc['hits']['total'] > 0){
			$this->set('title', __('Galerie pour le mot-clé "%s"', $keywordName));
			$this->execute($body);
// 		}
// 		else {
// 			throw new NotFoundException(__('Mot-clé "%s" inconnu', $keywordid));
// 		}

		
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
