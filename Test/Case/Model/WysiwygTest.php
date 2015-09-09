<?php
App::uses('Wysiwyg', 'Model');

/**
 * Wysiwyg Test Case
 *
 */
class WysiwygTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.wysiwyg'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Wysiwyg = ClassRegistry::init('Wysiwyg');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Wysiwyg);

		parent::tearDown();
	}

}
