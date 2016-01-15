<?php
use Elasticsearch\ClientBuilder;

App::uses('AppController', 'Controller');
/**
 * Flights Controller
 *
 * @property Flight $Flight
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class FlightsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $uses = array('Flight', 'Booking', 'Target');
	public $components = array();

	private $client;

	public function book($id) {
		
		$success = false;
		
		$this->Flight->id = $id;
		if (!$this->Flight->exists()) {
			throw new NotFoundException(__('Vol non-identifié'));
		}
		$this->Flight->locale = Configure::read('Config.language');
		$flight = $this->Flight->findById($id);
		
		$mapBB = [];
		if(isset($flight['Flight']['placeId'])){
			$searchParams = [
					'index' => Configure::read('Config.apertureIndex'),
					'type'  => Configure::read('Config.versionModel'),
					'body' => '
						{
						   "size": 0,
						   "query": {
						      "nested": {
						         "path": "locations",
						         "query": {
						            "term": {
						               "locations.uuid": {
						                  "value": '.json_encode($flight['Flight']['placeId']).'
						               }
						            }
						         }
						      }
						   },
						   "aggs": {
						      "by_locations": {
						         "nested": {
						            "path": "locations"
						         },
						         "aggs": {
						            "place": {
						               "filter": {
						                  "term": {
						                     "locations.uuid": '.json_encode($flight['Flight']['placeId']).'
						                  }
						               },
						               "aggs": {
						                  "sample": {
						                     "top_hits": {
						                        "size": 1
						                     }
						                  }
						               }
						            }
						         }
						      }
						   }
						}
					',
			];
			
			
			$retDoc = $this->client->search($searchParams);
			if($retDoc["hits"]["total"] > 0){
				$mapBB = [
					'north' => $retDoc["aggregations"]["by_locations"]["place"]["sample"]["hits"]["hits"][0]["_source"]["northeast"]["lat"],
					'south' => $retDoc["aggregations"]["by_locations"]["place"]["sample"]["hits"]["hits"][0]["_source"]["southwest"]["lat"],
					'west' => $retDoc["aggregations"]["by_locations"]["place"]["sample"]["hits"]["hits"][0]["_source"]["southwest"]["lon"],
					'est' => $retDoc["aggregations"]["by_locations"]["place"]["sample"]["hits"]["hits"][0]["_source"]["northeast"]["lon"],
				];
			}
			else {
				$mapBB = Configure::read('Config.defaultGoogleMap');
			}
		}
		else{
			$mapBB = Configure::read('Config.defaultGoogleMap');
		}
		
		if ($this->request->is(array('post', 'put'))) {
			$this->request->data['Booking']['flightId'] = $id; 		
			$this->request->data['Booking']['isContactRequest'] = false; 			
 			
			if ($this->Booking->save($this->request->data)) {
				$this->Session->setFlash(__('Votre demande à été envoyée.'));
				
				try {
					$multipleData = [];
					foreach ($this->request->data['Booking']['target'] as $target){
						$multipleData[] = [
							'comment' => $target["comment"],
							'polygon' => urldecode($target["polygon"]),
							'bookingId' => $this->Booking->id
						];
						
// 						echo $target["comment"]."\n";
// 						echo urldecode($target["polygon"])."\n\n";
					}
					if(sizeof($multipleData > 0)){
						$this->Target->saveMany($multipleData);						
					}
// 					exit;
				}
				catch (Exception $e){
					
				}
				
				$this->redirect([
						'controller' => 'pages',
						'action' => 'display',
						'language' => Configure::read('Config.language'),
						'thanks'
						
				]);
			}
			else {
				$this->Session->setFlash(__('Impossible d\'enregistrer votre demande. Réessayer plus tard.'));
			}
		}		
		$this->set('flight', $flight);
		$this->set('mapBB', $mapBB);
		
	}

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('book');
		$this->client = ClientBuilder::create()           // Instantiate a new ClientBuilder
			->setHosts(Configure::read('awsESHosts'))      // Set the hosts
			->build();              // Build the client object
	}


}
