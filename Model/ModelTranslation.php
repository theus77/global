<?php
App::uses('AppModel', 'Model');
/**
 * Gallery Model
 *
 */
class ModelTranslation extends AppModel {

	public $displayField = 'content';
	
	public $useTable = 'i18n';

}
