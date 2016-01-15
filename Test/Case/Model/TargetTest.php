<?php
App::uses('Target', 'Model');

/**
 * Target Test Case
 *
 */
class TargetTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.target'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Target = ClassRegistry::init('Target');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Target);

		parent::tearDown();
	}

}
