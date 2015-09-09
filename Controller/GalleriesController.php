<?php
App::uses('AppController', 'Controller');
/**
 * Galleries Controller
 *
 * @property Gallery $Gallery
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class GalleriesController extends AppController {

	public $components = array();
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('library', 'search');
	}
	
	
	public function library() {
		$model = $this->{$this->modelClass};
		//print_r($model); exit;
		$model->locale = Configure::read('Config.language');
		$model->recursive = 0;
		/*$model->conditions = array(
				'keyword' => true,
				'publish' => true,
				'keyword' => true
		);*/
		//$model->limit = false;
		//$model->order = 'Gallery__i18n_nameTranslation';
		$this->set($model->useTable, $model->find('all', array(
				'order' => 'Gallery__i18n_nameTranslation',
				'conditions' => array(
					'keyword' => true,
					'published' => true,
					'keyword' => true
				),
				''
		)));	
	}
	
	public function search() {
		$tok = false;
		if(isset($this->request->query['q'])){
			$tok = preg_split("/[\s,]+/", $this->request->query['q']);
		}
	
	
		$this->Gallery->locale = Configure::read('Config.language');
	
		$option = array(
				'limit' => 9,
				'conditions' => array(
						'published' => true,
						'AND' => array()
				),
				'order' => array('counter' => 'desc'),
		);
		
		
		foreach ($tok as $token){
			$option['conditions']['AND'][] = array('OR' => array(
					//'name like' => '%'.$token.'%',
					'zip like' => '%'.$token.'%',
					'content like ' => '%'.$token.'%'
			),
			);
		}
		
		$this->Paginator->limit = 24;
		$galleries = $this->Paginator->paginate($option['conditions']);
		$this->set('galleries', $galleries);

		//$galleries = $this->Gallery->find('all', $option);
		
		
		/*$this->request->params['paging']['Gallery'] = array(
				'count' => $this->Gallery->find('count', $option)
		);*
		
		
	
		/*$placeids = array(0);
			foreach ($galleries as &$gallery){
			if (preg_match ("/^(\/placeId\:)([0-9]*)$/", $gallery['Gallery']['url'], $regs)) {
			$placeids[] = $regs[2];
			}
			}
	
	
	
			$option = array(
			'conditions' => array(
			'PlaceName.language' => Configure::read('Config.language'),
			'AND' => array()
			),
			'fields' => array('placeId', 'description'),
			);
			if(count($placeids)>1){
			$option['conditions']['placeid not'] = $placeids;
			}
			foreach ($tok as $token){
			$option['conditions']['AND'][] = array('OR' => array(
			'description like ' => '%'.$token.'%'),
			);
			}
			$placeNames = $this->PlaceName->find('list', $option);
			$this->set('placeNames', $placeNames);
	
	
			$findOptions = array(
			'conditions' => array(
			'type >' => 16,
			'AND' => array(),
			),
			'fields' => array('modelId', 'defaultName'),
			);
			if(count($placeids)+count($placeNames) >1){
			$option['conditions']['modelid not'] = array_merge(array_keys($placeNames), $placeids);
			}
			foreach ($tok as $token){
			$findOptions['conditions']['AND'][] = array('OR' => array(
			'Place.defaultName like' => '%'.$token.'%',
			//'PlaceName.description like ' => '%'.$token.'%'
			),
			);
			}
			$place = $this->Place->find('list', $findOptions);
			$this->set('places', $place);
	
			/*
			'contain' => array(
			'PlaceName' => array(
			'conditions' => array('PlaceName.language' => Configure::read('Config.language'),)
			)),*/
	
		//print_r($galleries);
		//exit;
	}
	

	
	
}
