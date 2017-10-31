<?php

class JustSnapsTest extends \PHPUnit\Framework\TestCase {
	public function test_works() {
		$data = [
			'a' => 'b',
		];
		$snap_file_data = json_encode( $data );
		$snap_file_driver = JustSnaps\FileDriver::buildWithData( [
			'foobar' => $snap_file_data,
		] );
		$matcher = new JustSnaps\Matcher();
		$asserter = new JustSnaps\Asserter( $snap_file_driver, $matcher );
		$this->assertTrue( $asserter->forTest( 'foobar' )->assertMatchesSnapshot( $data ) );
	}
}
