<?php
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

class KeywordsController extends AppController {
	public $uses = array();
	public $components = array('RequestHandler', 'Paginator', 'Acl', 'Session');
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('index');
	}
	
	public function index(){
	
		$searchQuery['index'] = 'aperture';
		$searchQuery['type'] = 'version';
		$searchQuery['search_type'] = 'count';
// 		$searchQuery['body'] = json_decode('
// 		{
// 		  "size": 0,
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
// 		          }
// 		        }
// 		      }
// 		    }
// 		  }
// 		}');

		
		
		$searchQuery['body'] = json_decode('
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
			                  "field": "Keywords.name_'.Configure::read('Config.language').'.raw",
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
			}				

				');
		
		$client = ClientBuilder::create()           // Instantiate a new ClientBuilder
			->setHosts(Configure::read('awsESHosts'))      // Set the hosts
			->build();              // Build the client object
	
// 		echo json_encode($searchQuery['body']); exit;
			
		$retDoc = $client->search($searchQuery);
		
	
		$this->set('keywords', $retDoc['aggregations']['keywords']);
	}
	
	
}