<?php
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
	public $uses = array('Flight', 'ApertureConnector.Place');
	public $components = array();


	public function book($id) {
		$this->Flight->id = $id;
		if (!$this->Flight->exists()) {
			throw new NotFoundException(__('Vol non-identifié'));
		}
		$this->Flight->locale = Configure::read('Config.language');
		$flight = $this->Flight->findById($id);
		//$locations = $this->getLocations($place, $flight['Flight']['placeId']);
		//$place = $this->Place->findByModelid($flight['Flight']['placeId']);

		//$this->set('place', $place);
		$this->set('flight', $flight);
	}

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('book');
	}


}
