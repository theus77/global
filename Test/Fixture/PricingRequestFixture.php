<?php
/**
 * PricingRequestFixture
 *
 */
class PricingRequestFixture extends CakeTestFixture {

/**
 * Table name
 *
 * @var string
 */
	public $table = 'pricing_request';

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'treated' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'mainVersionUuid' => array('type' => 'string', 'null' => false, 'length' => 200, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'versionUuid' => array('type' => 'string', 'null' => false, 'length' => 200, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 1024, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'vat' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 200, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'email' => array('type' => 'string', 'null' => false, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'phone' => array('type' => 'string', 'null' => false, 'length' => 200, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'media' => array('type' => 'string', 'null' => false, 'length' => 200, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'printRun' => array('type' => 'string', 'null' => false, 'length' => 200, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'maxFormat' => array('type' => 'string', 'null' => false, 'length' => 200, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'duration' => array('type' => 'string', 'null' => false, 'length' => 200, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'special' => array('type' => 'string', 'null' => false, 'length' => 200, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'comment' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'created' => '2016-01-16 16:48:07',
			'modified' => '2016-01-16 16:48:07',
			'treated' => 1,
			'mainVersionUuid' => 'Lorem ipsum dolor sit amet',
			'versionUuid' => 'Lorem ipsum dolor sit amet',
			'name' => 'Lorem ipsum dolor sit amet',
			'vat' => 'Lorem ipsum dolor sit amet',
			'email' => 'Lorem ipsum dolor sit amet',
			'phone' => 'Lorem ipsum dolor sit amet',
			'media' => 'Lorem ipsum dolor sit amet',
			'printRun' => 'Lorem ipsum dolor sit amet',
			'maxFormat' => 'Lorem ipsum dolor sit amet',
			'duration' => 'Lorem ipsum dolor sit amet',
			'special' => 'Lorem ipsum dolor sit amet',
			'comment' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.'
		),
	);

}
