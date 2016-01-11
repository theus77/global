<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

	
	public $uses = array('Wysiwyg');
			
	public $components = array(
			'Session',
			'Auth' => array(
					'loginRedirect' => array('controller' => 'pages', 'action' => 'home'),
					'logoutRedirect' => array('controller' => 'pages', 'action' => 'home'),
        			'authorize' => array('Controller')
			),
			'RequestHandler',
			'Paginator',
	);
	
	
	public function fixUuid(&$uuid){
		$uuid = str_replace(' ', '+', $uuid);
		return $uuid;
	}
	
	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->Auth->allow('viewVersion', 's3');
		
		if(isset($this->request->params['language'])){
			Configure::write('Config.language', $this->request->params['language'] );
			

		}

		$this->Wysiwyg->locale = Configure::read('Config.language');
		$i18n =  $this->Wysiwyg->find('list', array(
				'fields' => array('key', 'value')
		));
		
		$this->set('i18n', $i18n);
	}	
	
	public function isAuthorized($user) {
		// Admin peut accéder à toute action
		if (isset($user['role']) && $user['role'] === 'admin') {
			return true;
		}
	
		// Refus par défaut
		return false;
	}
	

	public function add() {
		$success = false;
		$messsages = array();
		
		$model = $this->{$this->modelClass};
		
		if ($this->request->is('post')) {
			$model->create();
			$model->locale = DEFAULT_LANGUAGE;
			if ($model->save($this->request->data)) {
				$messsages[] = __('The model has been created.');
				//$this->request->data[$model->primaryKey] = $model->id;
				$this->request->data[$this->modelClass][$model->primaryKey] = $model->id;
				
				//print_r($this->request->data);
				
				//echo $this->request->data[$model->primaryKey] ; echo($model->id); print_r($this->request->data); exit;
				
				$messsages = array_merge($messsages, $this->saveTranslations($model));
				//exit;
				$success = true;
			} else {
				$messsages[] = __('The %s could not be saved. Please, try again.', $this->modelClass);
			}
			
			
			if(!$this->request->is('ajax')){
				//echo 'so far so  good'; exit;
				foreach ($messsages as $messsages){
					$this->Session->setFlash($messsages);
				}
				if(count($this->request->query) > 0)
					return $this->redirect($this->request->query);
				
				if($success){
					return $this->redirect(array('action' => 'view', $model->id, 'language' => Configure::read('Config.language')));
				}			
			}
		}
		
		$this->set('_serialize', array('success', 'messages'));
	}
	
	
	public function index() {
		
		$model = $this->{$this->modelClass};
		//print_r($model); exit;
		$model->locale = Configure::read('Config.language');
		$model->recursive = 0;
		$this->set($model->useTable, $this->Paginator->paginate());
	}
	
	public function endsWith($haystack, $needle) {
		// search forward starting from end minus needle length characters
		return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
	}
	
	private function saveTranslations($model){
		$messsages = array();
		$data = $this->request->data;
		$languages = Configure::read('Config.languages');
		$objects = array();
		
		foreach ($data as $field => $translations){
			if($this->endsWith($field, 'Translation')){
				foreach ($translations as $langId => $translation){
					if(!isset($objects[$langId])){
						$objects[$langId] = array($model->name => $data[$model->name]);
					}
					$objects[$langId][$model->name][substr($field, 0, strlen($field)-11)] = $translation['content'];
				}
			}
		}
		
		//print_r($objects);
		$langId = 0;
		foreach($languages as $code => $language){
			if(isset($objects[$langId])){
				$model->locale = $code;
				//print_r($objects[$langId]);
				if($model->save($objects[$langId])){
					$messsages[] = (__('The %s model has been saved', $language));
				} else {
					$messsages[] = (__('The %s translation could not be saved. Please, try again.', $language));
				}
			}
			//echo 'object '.$langId;
				
			++$langId;
		}
		return $messsages;
	}
	
	public function edit($id = null) {

		$model = $this->{$this->modelClass};
		
		$success = false;
		$messsages = array();
		
		if (!$model->exists($id)) {
			throw new NotFoundException(__('Invalid model'));
		}
		
		$model->locale = DEFAULT_LANGUAGE;
		if ($this->request->is(array('post', 'put'))) {
			if ($model->save($this->request->data)) {
				$messsages[] = __('The model has been saved.');
				
				$messsages = array_merge($messsages, $this->saveTranslations($model));
				$success = true;		
			} 
			else {
				$messsages[] = __('Impossible de sauver le model %s. Réessayer plus tard.', $model->name);
			}
		} 
		else {
			$options = array(
					'conditions' => array($model->name . '.' . $model->primaryKey => $id),
					'recursive' => 1,
			);
			$this->request->data = $model->find('first', $options);
		}
		
		
		$this->wrapResponse($success, $messsages, $id);

	}
	
	public function update($id = null) {

		$model = $this->{$this->modelClass};
		
		$success = false;
		$messsages = array();
		
		if (!$model->exists($id)) {
			throw new NotFoundException(__('Invalid model'));
		}
		
		
		$model->locale = Configure::read('Config.language');
		if ($this->request->is(array('post', 'put'))) {
			
			if ($model->save($this->request->data)) {
				$messsages[] = __('The model has been saved.');
				
				$messsages = array_merge($messsages, $this->saveTranslations($model));

				$success = true;		
			} 
			else {
				$messsages[] = __('Impossible de sauver le model %s. Réessayer plus tard.', $model->name);
			}
		} 
		else {
			$options = array(
					'conditions' => array($model->name . '.' . $model->primaryKey => $id),
					'recursive' => 1,
			);
			$this->request->data = $model->find('first', $options);
		}
		
		
		$this->wrapResponse($success, $messsages, $id);

	}
	
	private function wrapResponse($success, $messages, $id=false){
		if(!$this->request->is('ajax')){
			$flash = '';
			foreach ($messages as $message){
				$flash.=$message.'<br>';
			}
			$this->Session->setFlash($flash);
		
			if($success){
				if(count($this->request->query) > 0)
					return $this->redirect($this->request->query);
				else if($id)
					return $this->redirect(array('action' => 'view', 'language' => Configure::read('Config.language'), $id));
				return $this->redirect(array('action' => 'index', 'language' => Configure::read('Config.language')));
				
			}
		}
		$this->set('success', $success);
		$this->set('messages', $messages);
		
		$this->set('_serialize', array('success', 'messages'));
	}
	
	public function publish($id=null){
		$success = false;
		$messsages = array();
		
		$model = $this->{$this->modelClass};
		if (!$model->exists($id)) {
			throw new NotFoundException(__('Invalid model'));
		}
		
		if ($this->request->is(array('post', 'put'))) {
			$model->locale = DEFAULT_LANGUAGE;
			$data = $model->findById($id);
			$data[$this->modelClass]['published'] = (! $data[$this->modelClass]['published']);
			if ($model->save($data)) {
				$messsages[] = __('The %s has been saved.', $this->modelClass);
				$success = true;
			}
			else{
				$messsages[] = __('The %s hasn\'t been saved.', $this->modelClass);
			}
		}
		
		$this->wrapResponse($success, $messsages, $id);
		
	}
	
	public function view($id=null){
		$model = $this->{$this->modelClass};
		
		if (!$model->exists($id)) {
			throw new NotFoundException(__('Invalid %', $model->name));
		}

		$model->locale = Configure::read('Config.language');
		$options = array('conditions' => array($model->name.'.' . $model->primaryKey => $id));
		
		$this->set(lcfirst($model->name), $model->find('first', $options));
		
		$this->set('_serialize', array(lcfirst($model->name)));
	}
	
	public function delete($id){
		$success = false;
		$messsages = array();
		
		$model = $this->{$this->modelClass};
		$model->id = $id;
		if (!$model->exists()) {
			throw new NotFoundException(__('Invalid %s', $this->modelClass));
		}
		
		$this->request->allowMethod('post', 'delete');
		if ($model->delete()) {
			$success = true;
			$messsages[] = __('The model has been deleted.');
		} else {
			$messsages[] = __('The model could not be deleted. Please, try again.');
		}
		

		return $this->wrapResponse($success, $messsages);	
	}

}
