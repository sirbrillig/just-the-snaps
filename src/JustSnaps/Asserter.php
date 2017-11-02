<?php
declare(strict_types=1);

namespace JustSnaps;

/**
 * Factory to create instances of TestAsserter
 *
 * Each TestAsserter has a name which should be the name of the current test or
 * a hash representing the current test. This name is used as a key to the
 * FileDriverProvider to find or create a snapshot.
 */
class Asserter {
	private $driver;

	public function __construct(FileDriverProvider $driver) {
		$this->driver = $driver;
	}

	public function forTest(string $testName): TestAsserter {
		return new TestAsserter($testName, $this->driver);
	}

	public function addSerializer(Serializer $serializer) {
		$this->driver = FileDriver::addSerializerToDriver($serializer, $this->driver);
	}
}
