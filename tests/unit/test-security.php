<?php
/**
 * Tests for HeadlessProSecurity.
 *
 * @group unit
 * @package HeadlessPro\Tests\Unit
 */

class Test_HeadlessProSecurity extends WP_UnitTestCase {

	private HeadlessProSecurity $security;

	public function set_up(): void {
		parent::set_up();
		$this->security = new HeadlessProSecurity();
	}

	public function test_security_headers_filter_adds_correct_headers(): void {
		$headers = $this->security->security_headers_filter( array() );

		$this->assertCount( 3, $headers );
		$this->assertEquals( 'nosniff', $headers['X-Content-Type-Options'] );
		$this->assertEquals( 'SAMEORIGIN', $headers['X-Frame-Options'] );
		$this->assertEquals( '1; mode=block', $headers['X-XSS-Protection'] );
	}

	public function test_security_headers_filter_preserves_existing(): void {
		$headers = $this->security->security_headers_filter( array( 'Custom' => 'value' ) );

		$this->assertEquals( 'value', $headers['Custom'] );
		$this->assertCount( 4, $headers );
	}

	public function test_secure_rest_api_passes_through_unchanged(): void {
		$this->assertNull( $this->security->secure_rest_api( null ) );
		$this->assertTrue( $this->security->secure_rest_api( true ) );
		$this->assertFalse( $this->security->secure_rest_api( false ) );
	}
}
