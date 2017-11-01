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

	public function testAssertMatchesSnapshotCreatesSnapshotIfMissing() {
		$data = [
			'a' => 'b',
		];
		$snap_file_driver = FileDriver::buildWithData([]);
		$matcher = new Matcher();
		$asserter = new Asserter($snap_file_driver, $matcher);
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
		$snap_file_driver = FileDriver::buildWithData([]);
		$matcher = new Matcher();
		$asserter = new Asserter($snap_file_driver, $matcher);
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
		$snap_file_driver = FileDriver::buildWithData([]);
		$matcher = new Matcher();
		$asserter = new Asserter($snap_file_driver, $matcher);
		try {
			$asserter->forTest('foobar')->assertMatchesSnapshot($data);
		} catch (CreatedSnapshotException $err) {
			$err; // noop
		}
		$snap_file_driver->removeSnapshotForTest('foobar');
		$this->expectException(CreatedSnapshotException::class);
		$asserter->forTest('foobar')->assertMatchesSnapshot($data);
	}

	public function testAssertMatchesSnapshotFailsIfDataIsAdded() {
		$data = [
			'a' => 'b',
		];
		$snap_file_driver = FileDriver::buildWithData([]);
		$matcher = new Matcher();
		$asserter = new Asserter($snap_file_driver, $matcher);
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
		$snap_file_driver = FileDriver::buildWithData([]);
		$matcher = new Matcher();
		$asserter = new Asserter($snap_file_driver, $matcher);
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
		$snap_file_driver = FileDriver::buildWithDirectory('./tests/__snapshots__');
		$snap_file_driver->removeSnapshotForTest('foobar');
		$matcher = new Matcher();
		$asserter = new Asserter($snap_file_driver, $matcher);
		try {
			$asserter->forTest('foobar')->assertMatchesSnapshot($data);
		} catch (CreatedSnapshotException $err) {
			$err; // noop
		}
		$this->assertTrue($asserter->forTest('foobar')->assertMatchesSnapshot($data));
		$snap_file_driver->removeSnapshotForTest('foobar');
	}
}
