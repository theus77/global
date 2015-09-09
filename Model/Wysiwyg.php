<?php
App::uses('AppModel', 'Model');
/**
 * Wysiwyg Model
 *
 */
class Wysiwyg extends AppModel {

	
	public $actsAs = array(
			'Translate' => array(
					'value' => 'valueTranslation'
			)
	);

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'key';

}
