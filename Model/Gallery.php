<?php
App::uses('AppModel', 'Model');
/**
 * Gallery Model
 *
 */
class Gallery extends AppModel {
	
	public $actsAs = array(
			'Translate' => array(
				'name' => 'nameTranslation',
				'slug' => 'slugTranslation'
			)
	);
	
/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';
	
	public $order = 'weight';

}
