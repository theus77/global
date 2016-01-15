<?php
App::uses('Booking', 'Model');

/**
 * Booking Test Case
 *
 */
class BookingTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.booking'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Booking = ClassRegistry::init('Booking');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Booking);

		parent::tearDown();
	}

}
