<?php
/**
 * Tests for HeadlessProCORS.
 *
 * @group unit
 * @package HeadlessPro\Tests\Unit
 */

class Test_HeadlessProCORS extends WP_UnitTestCase {

	public function test_production_and_dev_origins_are_allowed(): void {
		$origins = HeadlessProConfig::get_allowed_origins();

		$this->assertContains( 'https://edrishusein.com', $origins );
		$this->assertContains( 'https://www.edrishusein.com', $origins );
		$this->assertContains( 'http://localhost:3000', $origins );
		$this->assertContains( 'http://localhost:3001', $origins );
	}

	public function test_disallowed_origin_not_in_list(): void {
		$origins = HeadlessProConfig::get_allowed_origins();

		$this->assertNotContains( 'https://evil.com', $origins );
	}
}
