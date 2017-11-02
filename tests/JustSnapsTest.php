<?php
declare(strict_types=1);

namespace JustSnaps;

class JustSnapsTest extends \PHPUnit\Framework\TestCase {
	public function testCreatesAsserterWithDefaults() {
		$data = [ 'a' => 'b' ];
		$snapFileDriver = FileDriver::buildWithDirectory('./tests/__snapshots__');
		$snapFileDriver->removeSnapshotForTest('foobar');
		$asserter = buildSnapshotAsserter('./tests/__snapshots__');
		try {
			$asserter->forTest('foobar')->assertMatchesSnapshot($data);
		} catch (CreatedSnapshotException $err) {
			$err; // noop
		}
		$this->assertTrue($asserter->forTest('foobar')->assertMatchesSnapshot($data));
		$snapFileDriver->removeSnapshotForTest('foobar');
	}
}
