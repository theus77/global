<?php
namespace AppBundle\Service;

use Elasticsearch\Client;
use function GuzzleHttp\json_decode;

class SearchService {

	const PAGE_SIZE = 100;
	
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
	
	public function execute($body, $p){
		$body['size'] = SearchService::PAGE_SIZE;
		$body['from'] = ($p*SearchService::PAGE_SIZE);
		$searchParams = [
				'index' => $this->apertureIndex,
				'type'  => 'version',
				'body' => $body,
		];
		
		return $this->client->search($searchParams);
	}
	
	public function keywordSearch($keywordUuid, $p){
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
		return $this->execute($body, $p);
	}
	
	public function geohashSearch($hash, $p){
		$body = json_decode('
			{
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
		return $this->execute($body, $p);
	}

	public function placeSearch($locationUuid, $p){
		$body = json_decode('
			{
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
		return $this->execute($body, $p);
	}

	public function freeSearch($query, $p, $locale){
		if(empty($query)){
			return $this->execute([], $p);
		}
		else {
			$body = json_decode('
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
					                          "Keywords.name_'.$locale.'": {
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
					                          "locations.name_'.$locale.'": {
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
						', true);

			return $this->execute($body, $p);
		}		
		
	}
	
}