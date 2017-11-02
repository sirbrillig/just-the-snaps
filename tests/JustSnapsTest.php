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

	public function testAddsSerializersToAsserter() {
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
		$this->assertEquals('{"foo":"bar","secret":"xxx"}', $snapFileDriver->getSnapshotForTest('foobar'));
		$snapFileDriver->removeSnapshotForTest('foobar');
	}
}
