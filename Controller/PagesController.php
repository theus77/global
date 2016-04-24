<?php
use Elasticsearch\ClientBuilder;

/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 */

App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController {

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array('Flight', 'Gallery', 'Booking');

	
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('display', 'home');
	}
	
	
/**
 * Displays a view
 *
 * @return void
 * @throws NotFoundException When the view file could not be found
 *   or MissingViewException in debug mode.
 */
	public function display() {
		$this->set('home', 'false');
		if ($this->request->is(array('post', 'put'))) {
			if(isset($this->request->data['Booking'])){
				$this->request->data['Booking']['isContactRequest'] = true;
			
				if ($this->Booking->save($this->request->data)) {
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
		
		
		
		$path = func_get_args();

		$count = count($path);
		if (!$count) {
			return $this->redirect('/');
		}
		$page = $subpage = $title_for_layout = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}
		$this->set(compact('page', 'subpage', 'title_for_layout'));

		try {
			$this->render(implode('/', $path));
		} catch (MissingViewException $e) {
			if (Configure::read('debug')) {
				throw $e;
			}
			throw new NotFoundException();
		}
	}
	
	public function home() {
		$this->layout = 'home';
		$this->set('home', 'true');
		$options = array(
				'conditions' => array(),
		);
		
		
		if(! AuthComponent::user()){
			$options['conditions']['published'] = true;
		}
		
		
		$client = ClientBuilder::create()           // Instantiate a new ClientBuilder
		->setHosts(Configure::read('awsESHosts'))      // Set the hosts
		->build();              // Build the client object
		
		
		$searchParams = [
			'index' => Configure::read('Config.apertureIndex'),
			'type'  => Configure::read('Config.versionModel'),
			'body' => '
				{
				   "size": 0,
				   "aggs": {
				     "counter": {
				       "cardinality": {
				         "field": "Stack"
				       }
				     }
				   }
				}				
			',
		];
		
		
		$retDoc = $client->search($searchParams);

		$this->set('serieCount', $retDoc["hits"]["total"]);
		$this->set('photoCount', $retDoc["aggregations"]["counter"]["value"]);
		
		$this->Flight->locale = Configure::read('Config.language');
		$this->set('flights', $this->Flight->find('all', $options));

		$options['conditions']['homepage'] = true;
		$this->Gallery->locale = Configure::read('Config.language');
		$this->set('galleries', $this->Gallery->find('all', $options));		

	}
}
