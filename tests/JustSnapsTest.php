<?php
declare(strict_types=1);

namespace JustSnaps;

class JustSnapsTest extends \PHPUnit\Framework\TestCase {
	public function testAssertMatchesSnapshot() {
		$data = [
			'a' => 'b',
		];
		$snap_file_data = json_encode($data);
		$snapFileDriver = FileDriver::buildWithData([
			'foobar' => $snap_file_data,
		]);
		$matcher = new Matcher();
		$asserter = new Asserter($snapFileDriver, $matcher);
		$this->assertTrue($asserter->forTest('foobar')->assertMatchesSnapshot($data));
	}

	public function testAssertMatchesSnapshotCreatesSnapshotIfMissing() {
		$data = [
			'a' => 'b',
		];
		$snapFileDriver = FileDriver::buildWithData([]);
		$matcher = new Matcher();
		$asserter = new Asserter($snapFileDriver, $matcher);
		try {
			$asserter->forTest('foobar')->assertMatchesSnapshot($data);
		} catch (CreatedSnapshotException $err) {
			$err; // noop
		}
		$this->assertTrue($asserter->forTest('foobar')->assertMatchesSnapshot($data));
	}

	public function testAssertMatchesSnapshotFailsIfDataChanges() {
		$data = [
			'a' => 'b',
		];
		$snapFileDriver = FileDriver::buildWithData([]);
		$matcher = new Matcher();
		$asserter = new Asserter($snapFileDriver, $matcher);
		try {
			$asserter->forTest('foobar')->assertMatchesSnapshot($data);
		} catch (CreatedSnapshotException $err) {
			$err; // noop
		}
		$data['a'] = 'x';
		$this->assertFalse($asserter->forTest('foobar')->assertMatchesSnapshot($data));
	}

	public function testAssertMatchesSnapshotFailsIfSnapshotRemoved() {
		$data = [
			'a' => 'b',
		];
		$snapFileDriver = FileDriver::buildWithData([]);
		$matcher = new Matcher();
		$asserter = new Asserter($snapFileDriver, $matcher);
		try {
			$asserter->forTest('foobar')->assertMatchesSnapshot($data);
		} catch (CreatedSnapshotException $err) {
			$err; // noop
		}
		$snapFileDriver->removeSnapshotForTest('foobar');
		$this->expectException(CreatedSnapshotException::class);
		$asserter->forTest('foobar')->assertMatchesSnapshot($data);
	}

	public function testAssertMatchesSnapshotFailsIfDataIsAdded() {
		$data = [
			'a' => 'b',
		];
		$snapFileDriver = FileDriver::buildWithData([]);
		$matcher = new Matcher();
		$asserter = new Asserter($snapFileDriver, $matcher);
		try {
			$asserter->forTest('foobar')->assertMatchesSnapshot($data);
		} catch (CreatedSnapshotException $err) {
			$err; // noop
		}
		$data['b'] = 'x';
		$this->assertFalse($asserter->forTest('foobar')->assertMatchesSnapshot($data));
	}

	public function testAssertMatchesSnapshotFailsIfDataIsRemoved() {
		$data = [
			'a' => 'b',
			'c' => 'd',
		];
		$snapFileDriver = FileDriver::buildWithData([]);
		$matcher = new Matcher();
		$asserter = new Asserter($snapFileDriver, $matcher);
		try {
			$asserter->forTest('foobar')->assertMatchesSnapshot($data);
		} catch (CreatedSnapshotException $err) {
			$err; // noop
		}
		$dataChanged = [ 'a' => 'b' ];
		$this->assertFalse($asserter->forTest('foobar')->assertMatchesSnapshot($dataChanged));
	}

	public function testFileDriverDirectory() {
		$data = [
			'a' => 'b',
		];
		$snapFileDriver = FileDriver::buildWithDirectory('./tests/__snapshots__');
		$snapFileDriver->removeSnapshotForTest('foobar');
		$matcher = new Matcher();
		$asserter = new Asserter($snapFileDriver, $matcher);
		try {
			$asserter->forTest('foobar')->assertMatchesSnapshot($data);
		} catch (CreatedSnapshotException $err) {
			$err; // noop
		}
		$this->assertTrue($asserter->forTest('foobar')->assertMatchesSnapshot($data));
		$snapFileDriver->removeSnapshotForTest('foobar');
	}

	public function testBuildWithDirectory() {
		$data = [
			'a' => 'b',
		];
		$snapFileDriver = FileDriver::buildWithDirectory('./tests/__snapshots__');
		$snapFileDriver->removeSnapshotForTest('foobar');
		$asserter = buildWithDirectory('./tests/__snapshots__');
		try {
			$asserter->forTest('foobar')->assertMatchesSnapshot($data);
		} catch (CreatedSnapshotException $err) {
			$err; // noop
		}
		$this->assertTrue($asserter->forTest('foobar')->assertMatchesSnapshot($data));
		$snapFileDriver->removeSnapshotForTest('foobar');
	}
}
