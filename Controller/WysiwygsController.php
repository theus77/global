<?php
App::uses('AppController', 'Controller');
/**
 * Wysiwygs Controller
 *
 * @property Wysiwyg $Wysiwyg
 * @property PaginatorComponent $Paginator
 * @property AclComponent $Acl
 * @property SessionComponent $Session
 */
class WysiwygsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('RequestHandler', 'Paginator', 'Acl', 'Session');

/**
 * index method
 *
 * @return void
 */
	public function index($singleline=false) {
		$this->Wysiwyg->locale = DEFAULT_LANGUAGE;// Configure::read('Config.language');
//		$this->Wysiwyg->recursive = 1;
// 		$this->Wysiwyg->conditions = array('singleline' => 1);
		$this->set('wysiwygs', $this->Paginator->paginate(array(
				'singleline' => $singleline
		)));
		//$this->view('index');
	}	

	
	public function text() {
		$this->index(true);
		$this->view = 'index';
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Wysiwyg->locale = DEFAULT_LANGUAGE;
			$this->Wysiwyg->create();
			$data = $this->Wysiwyg->save($this->request->data);
			if ($data) {
				$this->Session->setFlash(__('The wysiwyg has been saved'));
				$data['Wysiwyg']['value'] = /*__('Texte à traduire: ')./**/$data['Wysiwyg']['value'];
				
				foreach(Configure::read('Config.languages') as $code => $language) {
					if($code != DEFAULT_LANGUAGE){
						$this->Wysiwyg->locale = $code;
						
						$this->Wysiwyg->create();
						if($this->Wysiwyg->save($data)){
							$this->Session->setFlash(__('The wysiwyg has been saved'));
						} else {
							$this->Session->setFlash(__('The wysiwyg could not be saved. Please, try again.'));
						}	
					}
				}
				return $this->redirect(array('action' => 'edit', $data['Wysiwyg']['id']));
			} else {
				$this->Session->setFlash(__('The wysiwyg could not be saved. Please, try again.'));
			}			
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {		
		if(isset($this->request->params['named']['slug'])){
			$data = $this->Wysiwyg->findByKey($this->request->params['named']['slug']);
			if($data){
				$id = $data['Wysiwyg']['id'];
			}
			else {
				//TODO create for the key
			}
		}
		
		
		if (!$this->Wysiwyg->exists($id)) {
			throw new NotFoundException(__('Invalid wysiwyg'));
		}
		if ($this->request->is(array('post', 'put'))) {
			$this->Wysiwyg->locale = DEFAULT_LANGUAGE;
			if ($this->Wysiwyg->save($this->request->data)) {
				$this->Session->setFlash(__('The wysiwyg has been saved.'));
				
				$i = 0;
				$data = $this->request->data;
				foreach(Configure::read('Config.languages') as $code => $language) {
					if($code != DEFAULT_LANGUAGE){
						$this->Wysiwyg->locale = $code;
						$data['Wysiwyg']['value'] = $data['valueTranslation'][$i]['content'];
						if($this->Wysiwyg->save($data)){
							$this->Session->setFlash(__('The wysiwyg has been saved'));
						} else {
							$this->Session->setFlash(__('The wysiwyg could not be saved. Please, try again.'));
						}	
					}
					++$i;
				}
				
				
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The wysiwyg could not be saved. Please, try again.'));
			}
		} else {

			$this->Wysiwyg->locale = DEFAULT_LANGUAGE;
			$data = $this->Wysiwyg->findById($id);
			$this->set('wysiwyg', $data);
			$this->request->data = $data;
		}
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Wysiwyg->id = $id;
		if (!$this->Wysiwyg->exists()) {
			throw new NotFoundException(__('Invalid wysiwyg'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Wysiwyg->delete()) {
			$this->Session->setFlash(__('The wysiwyg has been deleted.'));
		} else {
			$this->Session->setFlash(__('The wysiwyg could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
	
	public function update($id = null){
		if($this->request->is('post') && isset($this->request->params['named']['slug'])){
			//$data = $this->request->input('json_decode');
			$success = false;
			$message ='';
			$this->Wysiwyg->locale = Configure::read('Config.language');
			$data = $this->Wysiwyg->findByKey($this->request->params['named']['slug']);
			if($data){
				$data['Wysiwyg']['value'] = $this->request->data('content');
				$success = $this->Wysiwyg->save($data);
			}
			else{
				if($this->Wysiwyg->create(array(
					'Wysiwyg' => array(
						'key' => $this->request->params['named']['slug'],
						'value' => $this->request->data('content'),
					)
				))){
					$success = $this->Wysiwyg->save();
				}
				else {
					$message(__('Impossible de créer une traduction pour la clé %s.', $this->request->params['named']['slug']));
				}
			}
			//if($success)
				//$success = true;
			print_r($this->request->data('content'));
			print_r($success);
			print_r($message);
			exit;		
			$this->set('_serialize', array('sucess', 'message'));
		}
		else{
			throw new BadRequestException(__('Requête incomplète.'));
		}
	}


}
