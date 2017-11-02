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
		$asserter = new Asserter($snapFileDriver);
		$this->assertTrue($asserter->forTest('foobar')->assertMatchesSnapshot($data));
	}

	public function testThrowsExceptionIfSnapshotIsMissing() {
		$data = ['a' => 'b'];
		$snapFileDriver = FileDriver::buildWithData([]);
		$asserter = new Asserter($snapFileDriver);
		$this->expectException(CreatedSnapshotException::class);
		$asserter->forTest('foobar')->assertMatchesSnapshot($data);
	}

	public function testCreatesSnapshotIfMissing() {
		$data = ['a' => 'b'];
		$snapFileDriver = FileDriver::buildWithData([]);
		$asserter = new Asserter($snapFileDriver);
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
		$asserter = new Asserter($snapFileDriver);
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
		$asserter = new Asserter($snapFileDriver);
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
		$asserter = new Asserter($snapFileDriver);
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
		$asserter = new Asserter($snapFileDriver);
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
		$asserter = new Asserter($snapFileDriver);
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
		$asserter = new Asserter($snapFileDriver);
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
		$snapFileDriver = FileDriver::addSerializerToDriver($serializer, FileDriver::buildWithData([]));
		$asserter = new Asserter($snapFileDriver);
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
		$snapFileDriver = FileDriver::addSerializerToDriver($serializer, FileDriver::buildWithData([]));
		$asserter = new Asserter($snapFileDriver);
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
		$snapFileDriver = FileDriver::addSerializerToDriver($serializer, FileDriver::buildWithData([]));
		$asserter = new Asserter($snapFileDriver);
		try {
			$asserter->forTest('foobar')->assertMatchesSnapshot($data);
		} catch (CreatedSnapshotException $err) {
			$err; // noop
		}
		$this->assertEquals(['a' => 'b'], json_decode($snapFileDriver->getSnapshotForTest('foobar'), true));
	}

	public function testAllowsAddingASerializerDirectly() {
		$actual = [ 'foo' => 'bar', 'secret' => 'thisisasecretpassword' ];
		$snapFileDriver = FileDriver::buildWithDirectory('./tests/__snapshots__');
		$snapFileDriver->removeSnapshotForTest('foobar');
		$printer = new class implements SerializerPrinter {
			public function serializeData($outputData) {
				if (isset($outputData['secret'])) {
					$outputData['secret'] = 'xxx';
				}
				return $outputData;
			}
		};
		$tester = new class implements SerializerTester {
			public function shouldSerialize($outputData): bool {
				return is_array($outputData);
			}
		};
		$serializer = new Serializer($tester, $printer);
		$asserter = \JustSnaps\buildSnapshotAsserter('./tests/__snapshots__');
		$asserter->addSerializer($serializer);
		try {
			$asserter->forTest('foobar')->assertMatchesSnapshot($actual);
		} catch (CreatedSnapshotException $err) {
			$err; //noop
		}
		$this->assertEquals(['foo' => 'bar','secret' => 'xxx'], json_decode($snapFileDriver->getSnapshotForTest('foobar'), true));
		$snapFileDriver->removeSnapshotForTest('foobar');
	}

	public function testAllowsAddingMultipleSerializers() {
		$actual = [ 'foo' => 'bar', 'secret' => 'thisisasecretpassword' ];
		$snapFileDriver = FileDriver::buildWithDirectory('./tests/__snapshots__');
		$snapFileDriver->removeSnapshotForTest('foobar');
		$printer1 = new class implements SerializerPrinter {
			public function serializeData($outputData) {
				$outputData['secret'] = 'xxx';
				return $outputData;
			}
		};
		$tester1 = new class implements SerializerTester {
			public function shouldSerialize($outputData): bool {
				return is_array($outputData) && isset($outputData['secret']);
			}
		};
		$printer2 = new class implements SerializerPrinter {
			public function serializeData($outputData) {
				$outputData['color'] = 'blue';
				return $outputData;
			}
		};
		$tester2 = new class implements SerializerTester {
			public function shouldSerialize($outputData): bool {
				return is_array($outputData);
			}
		};
		$serializer1 = new Serializer($tester1, $printer1);
		$serializer2 = new Serializer($tester2, $printer2);
		$asserter = \JustSnaps\buildSnapshotAsserter('./tests/__snapshots__');
		$asserter->addSerializer($serializer1);
		$asserter->addSerializer($serializer2);
		try {
			$asserter->forTest('foobar')->assertMatchesSnapshot($actual);
		} catch (CreatedSnapshotException $err) {
			$err; //noop
		}
		$this->assertEquals(['foo' => 'bar','secret' => 'xxx', 'color' => 'blue'], json_decode($snapFileDriver->getSnapshotForTest('foobar'), true));
		$snapFileDriver->removeSnapshotForTest('foobar');
	}

	public function testCreatesSnapshotDirectoryIfMissing() {
		$actual = [ 'foo' => 'bar' ];
		$directory = './tests/snapshotdir/nonexistentsnapshotdirectory';
		$snapFileDriver = FileDriver::buildWithDirectory($directory);
		$snapshot = $snapFileDriver->getSnapshotFileName('foobar');
		@unlink($snapshot);
		@rmdir($directory);
		$asserter = \JustSnaps\buildSnapshotAsserter($directory);
		try {
			$asserter->forTest('foobar')->assertMatchesSnapshot($actual);
		} catch (CreatedSnapshotException $err) {
			$err; //noop
		}
		$this->assertFileExists($snapshot);
		unlink($snapshot);
		rmdir($directory);
	}
}
