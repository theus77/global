<?php
/**
 * Application level View Helper
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Helper
 * @since         CakePHP(tm) v 0.2.9
 */

App::uses('Helper', 'View');

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class AppHelper extends Helper {
	
	
	public function getGalleryUrl($input){
		$url = array(
				'controller' => 'aperture',
				'action' => 'gallery',
				'language' => Configure::read('Config.language')
		);
		
		$args = explode("/", $input);
		
		foreach ($args as &$arg){
			if(strlen($arg) > 0){
				$sub = explode(":", $arg);
				if(count($sub) == 2){
					$url[$sub[0]] = $sub[1];
				}
				else {
					$url[] = $arg;
				}
			}
		}
		
		return $url;
	}

}
