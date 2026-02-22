<?php
/**
 * Tests for HeadlessProPerformance.
 *
 * @group unit
 * @package HeadlessPro\Tests\Unit
 */

class Test_HeadlessProPerformance extends WP_UnitTestCase {

	public function test_remove_dns_prefetch_filters_only_dns_prefetch(): void {
		$performance = new HeadlessProPerformance();
		$urls        = array( 'example.com', 'cdn.example.com' );

		// Non dns-prefetch relation should pass through unchanged.
		$this->assertEquals( $urls, $performance->remove_dns_prefetch( $urls, 'preconnect' ) );

		// dns-prefetch relation should be filtered.
		$result = $performance->remove_dns_prefetch( $urls, 'dns-prefetch' );
		$this->assertIsArray( $result );
	}
}
