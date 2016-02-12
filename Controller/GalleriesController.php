<?php
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Common\Exceptions\Missing404Exception;

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

	public $uses = array('Gallery', 'ModelTranslation', 'PricingRequest');
	public $components = array('RequestHandler', 'Paginator', 'Acl', 'Session');
	private $client;

	const PAGING_SIZE = 20;

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('index', 'search', 'keyword', 'view', 'place', 'geohash', 'price');
		$this->client = ClientBuilder::create()           // Instantiate a new ClientBuilder
			->setHosts(Configure::read('awsESHosts'))      // Set the hosts
			->build();              // Build the client object
	}



	private function execute($body){


// 		var_dump($body); exit;
		if(!isset($body['size'])){
			$body['size'] = self::PAGING_SIZE;
		}


// 		var_dump($this->request);exit;

		$page = 0;
		if(isset($this->request->query["page"])){
			$page = $this->request->query["page"];
			$body['from'] = $page * self::PAGING_SIZE;
		}

// 		var_dump($body);exit;

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


			$next = $page+1;
			if( $next * self::PAGING_SIZE < $retDoc['hits']['total']) {
				$this->set('next', $next);
			}
			if($page > 0) {
				$prev = $page - 1;
				$this->set('prev', $prev);
			}


			$this->set('page', $page);

			$this->set('pageMax', ceil( $retDoc['hits']['total'] / self::PAGING_SIZE ) );
		}
		else {
			$this->view = 'empty';
		}
		return $retDoc;
	}



	public function place($locationUuid) {

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
		', true);

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
		', true);

		$retDoc = $this->execute($body);

		$elemTitle = isset($retDoc["hits"]["hits"][0]["_source"]["label"])?$retDoc["hits"]["hits"][0]["_source"]["label"]:$retDoc["hits"]["hits"][0]["_source"]["name"];

		if($retDoc["hits"]["total"] > 1){
			$this->set('title', __('Galerie pour "%s" et aux alentours', $elemTitle));
		}
		else {
			$this->set('title', __('Galerie pour la serie "%s"', $elemTitle));
		}
	}

	public function version($uuid) {
	
		$body = json_decode('
			{
			    "query" : {
			        "term" : { "uuid" : '.json_encode($uuid).' }
			    }
			}
		', true);
	
		$retDoc = $this->execute($body);
	
		$elemTitle = isset($retDoc["hits"]["hits"][0]["_source"]["label"])?$retDoc["hits"]["hits"][0]["_source"]["label"]:$retDoc["hits"]["hits"][0]["_source"]["name"];
		$this->set('title', __('Galerie pour la serie "%s"', $elemTitle));
	}
	
	
	public function keyword($keywordUuid) {


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
		                          "Keywords.uuid": {
		                "query":    '. json_encode($keywordUuid) . ',
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
		}', true);

		$ret = $this->execute($body);

		$this->set('title', __('Galerie pour le mot-clé "%s"', $keywordUuid));

// 		var_dump($ret["hits"]["hits"][0]["_source"]["Keywords"]); exit;
		foreach ($ret["hits"]["hits"][0]["_source"]["Keywords"] as $location){
			if(strcmp($location['uuid'], $keywordUuid) == 0){
				$this->set('title', __('Galerie pour le mot-clé "%s"', $location['name_'.Configure::read('Config.language')]));
			}
		}

	}


	public function view ($slug = NULL) {


		$translation = $this->ModelTranslation->find('first', [
				'conditions' => [
						'model' => 'Gallery',
						'field' => 'slug',
						'content' => $slug
				]
		]);/**/
// 		var_dump($this->ModelTranslation); exit;

		if (!$translation) {
			throw new NotFoundException(__('Pas de galerie trouvée pour cette clé %', $slug));
		}

		$id = $translation["ModelTranslation"]["foreign_key"];

		if (!$this->Gallery->exists($id)) {
			throw new NotFoundException(__('Galerie inconnue'));
		}

		$this->Gallery->locale = Configure::read('Config.language');
		$options = ['conditions' => ['Gallery.id' => $id]];

		$gallery = $this->Gallery->find('first', $options);

		$ret = $this->execute(json_decode($gallery['Gallery']['query'], true));

		if(isset($slug) && $ret['hits']['total'] != $gallery['Gallery']['counter']){
			try {
				$gallery['Gallery']['counter'] = $ret['hits']['total'];
				$this->Gallery->save($gallery);
			}
			catch (Exception $e){

			}
		}

		$this->set('title', __('Galerie %s', $gallery['Gallery']['name']));
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


	public function price($versionUuid=NULL) {
		$searchParams = [
				'index' => Configure::read('Config.apertureIndex'),
				'type'  => Configure::read('Config.versionModel'),
				'id' => $versionUuid,
		];
		try {
			$version = $this->client->get($searchParams);
		}
		catch (Missing404Exception $e){
			throw new NotFoundException(__('Référence inconnue'));
		}


		if ($this->request->is(array('post', 'put'))) {
			if(isset($this->request->data['PricingRequest'])){
				$this->request->data['PricingRequest']['mainVersionUuid'] = $versionUuid;
				$this->request->data['PricingRequest']['treated'] = false;

				if ($this->PricingRequest->save($this->request->data)) {
					$this->Session->setFlash(__('Votre demande à été envoyée.'));
					$this->redirect([
							'controller' => 'pages',
							'action' => 'display',
							'language' => Configure::read('Config.language'),
							'thanks'
					]);
				}
			}
			else {
				$this->Session->setFlash(__('Impossible d\'enregistrer votre demande. Réessayer plus tard.'));
			}
		}

		$this->set('version', $version);

// 		var_dump($version); exit;
	}

	public function search($query = NULL) {

		if(isset($this->request->query['q'])){
			$query = $this->request->query['q'];
			$this->redirect([
					'language' => Configure::read('Config.language'),
					$query
			]);
		}
		else{
			if(!isset($query)){
				$this->set('title', __('Toute la photothèque'));
				$this->execute([]);
			}
			else {
				$this->set('query', $query);
				$this->set('title', __('Résultat de la recherche pour "%s"', $query));
				$this->execute(json_decode('
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
					              "match": {
					                "name": {
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
						', true));
			}
		}

	}





	public function admin () {
		$this->Gallery->locale = DEFAULT_LANGUAGE;

		$this->set('galleries', $this->Gallery->find('all'));
	}


}
