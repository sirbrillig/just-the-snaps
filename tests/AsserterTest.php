<?php
declare(strict_types=1);

namespace JustSnaps;

class AsserterTest extends \PHPUnit\Framework\TestCase {
	public function testMatchesSnapshotsThatMatch() {
		$data = ['a' => 'b'];
		$encodedData = json_encode($data);
		$snapFileDriver = FileDriver::buildWithData([
			'foobar' => $encodedData,
		]);
		$matcher = new Matcher();
		$asserter = new Asserter($snapFileDriver, $matcher);
		$this->assertTrue($asserter->forTest('foobar')->assertMatchesSnapshot($data));
	}

	public function testThrowsExceptionIfSnapshotIsMissing() {
		$data = ['a' => 'b'];
		$snapFileDriver = FileDriver::buildWithData([]);
		$matcher = new Matcher();
		$asserter = new Asserter($snapFileDriver, $matcher);
		$this->expectException(CreatedSnapshotException::class);
		$asserter->forTest('foobar')->assertMatchesSnapshot($data);
	}

	public function testCreatesSnapshotIfMissing() {
		$data = ['a' => 'b'];
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

	public function testFailsIfSnapshotIsDifferent() {
		$data = ['a' => 'b'];
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

	public function testFailsIfSnapshotIsRemoved() {
		$data = ['a' => 'b'];
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

	public function testFailsIfDataIsAdded() {
		$data = ['a' => 'b'];
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

	public function testFailsIfDataIsRemoved() {
		$data = ['a' => 'b', 'c' => 'd'];
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

	public function testDoesNotCreateSnapshotIfCreatingIsDisabled() {
		$data = ['a' => 'b' ];
		$snapFileDriver = FileDriver::makeReadOnly(FileDriver::buildWithData([]));
		$matcher = new Matcher();
		$asserter = new Asserter($snapFileDriver, $matcher);
		try {
			$asserter->forTest('foobar')->assertMatchesSnapshot($data);
		} catch (CreatedSnapshotException $err) {
			$err; // noop
			$this->fail();
		}
		$this->assertFalse($asserter->forTest('foobar')->assertMatchesSnapshot($data));
	}

	public function testMatchesWhenUsingFileBasedSnapshots() {
		$data = [ 'a' => 'b' ];
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

	public function testMatchesDataPassedThroughSerializer() {
		$data = [ 'a' => 'b' ];
		$printer = new class implements SerializerPrinter {
			public function serializeData($outputData) {
				$outputData['serialized'] = 'yup';
				return $outputData;
			}
		};
		$tester = new class implements SerializerTester {
			public function shouldSerialize($outputData): bool {
				return is_array($outputData);
			}
		};
		$serializer = new Serializer($tester, $printer);
		$snapFileDriver = FileDriver::addSerializer($serializer, FileDriver::buildWithData([]));
		$matcher = new Matcher();
		$asserter = new Asserter($snapFileDriver, $matcher);
		try {
			$asserter->forTest('foobar')->assertMatchesSnapshot($data);
		} catch (CreatedSnapshotException $err) {
			$err; // noop
		}
		$this->assertTrue($asserter->forTest('foobar')->assertMatchesSnapshot($data));
	}

	public function testPassesSnapshotDataThroughSerializer() {
		$data = [ 'a' => 'b' ];
		$printer = new class implements SerializerPrinter {
			public function serializeData($outputData) {
				$outputData['serialized'] = 'yup';
				return $outputData;
			}
		};
		$tester = new class implements SerializerTester {
			public function shouldSerialize($outputData): bool {
				return is_array($outputData);
			}
		};
		$serializer = new Serializer($tester, $printer);
		$snapFileDriver = FileDriver::addSerializer($serializer, FileDriver::buildWithData([]));
		$matcher = new Matcher();
		$asserter = new Asserter($snapFileDriver, $matcher);
		try {
			$asserter->forTest('foobar')->assertMatchesSnapshot($data);
		} catch (CreatedSnapshotException $err) {
			$err; // noop
		}
		$this->assertEquals('{"a":"b","serialized":"yup"}', $snapFileDriver->getSnapshotForTest('foobar'));
	}

	public function testIgnoresSerializersThatShouldNotBeUsed() {
		$data = [ 'a' => 'b' ];
		$printer = new class implements SerializerPrinter {
			public function serializeData($outputData) {
				$outputData['serialized'] = 'yup';
				return $outputData;
			}
		};
		$tester = new class implements SerializerTester {
			public function shouldSerialize($outputData): bool {
				return is_string($outputData);
			}
		};
		$serializer = new Serializer($tester, $printer);
		$snapFileDriver = FileDriver::addSerializer($serializer, FileDriver::buildWithData([]));
		$matcher = new Matcher();
		$asserter = new Asserter($snapFileDriver, $matcher);
		try {
			$asserter->forTest('foobar')->assertMatchesSnapshot($data);
		} catch (CreatedSnapshotException $err) {
			$err; // noop
		}
		$this->assertEquals('{"a":"b"}', $snapFileDriver->getSnapshotForTest('foobar'));
	}
}