<?php
declare(strict_types=1);

namespace JustSnaps;

class JustSnapsTest extends \PHPUnit\Framework\TestCase {
	public function testAssertMatchesSnapshot() {
		$data = [
			'a' => 'b',
		];
		$snap_file_data = json_encode($data);
		$snap_file_driver = FileDriver::buildWithData([
			'foobar' => $snap_file_data,
		]);
		$matcher = new Matcher();
		$asserter = new Asserter($snap_file_driver, $matcher);
		$this->assertTrue($asserter->forTest('foobar')->assertMatchesSnapshot($data));
	}

	public function testFileDriverDirectory() {
		$data = [
			'a' => 'b',
		];
		$snap_file_driver = FileDriver::buildWithDirectory('./tests/__snapshots__');
		$matcher = new Matcher();
		$asserter = new Asserter($snap_file_driver, $matcher);
		$this->assertTrue($asserter->forTest('foobar')->assertMatchesSnapshot($data));
	}
}
