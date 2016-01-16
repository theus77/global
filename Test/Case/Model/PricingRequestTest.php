<?php
App::uses('PricingRequest', 'Model');

/**
 * PricingRequest Test Case
 *
 */
class PricingRequestTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.pricing_request'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->PricingRequest = ClassRegistry::init('PricingRequest');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->PricingRequest);

		parent::tearDown();
	}

}
