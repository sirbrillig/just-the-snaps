<?php
declare(strict_types=1);

namespace JustSnaps;

/**
 * Factory to create instances of TestAsserter
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
		$this->driver = FileDriver::addSerializer($serializer, $this->driver);
	}
}
