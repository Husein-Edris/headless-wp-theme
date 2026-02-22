<?php
/**
 * Tests for HeadlessProConfig.
 *
 * @group unit
 * @package HeadlessPro\Tests\Unit
 */

class Test_HeadlessProConfig extends WP_UnitTestCase {

	public function test_get_frontend_url_returns_valid_url_without_trailing_slash(): void {
		$url = HeadlessProConfig::get_frontend_url();

		$this->assertEquals( 'https://edrishusein.com', $url );
		$this->assertStringEndsNotWith( '/', $url );
	}

	public function test_get_allowed_origins_contains_all_expected_values(): void {
		$origins = HeadlessProConfig::get_allowed_origins();

		$this->assertCount( 7, $origins );
		$this->assertContains( 'http://localhost:3000', $origins );
		$this->assertContains( 'http://localhost:3001', $origins );
		$this->assertContains( 'https://edrishusein.com', $origins );
		$this->assertContains( 'https://www.edrishusein.com', $origins );
		$this->assertContains( 'https://magical-swirles.82-165-132-190.plesk.page', $origins );
		$this->assertContains( 'http://82.165.132.190', $origins );
		$this->assertContains( 'https://82.165.132.190', $origins );
		$this->assertNotContains( 'https://evil.com', $origins );
	}
}
