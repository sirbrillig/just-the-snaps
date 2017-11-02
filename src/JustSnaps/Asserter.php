<?php
declare(strict_types=1);

namespace JustSnaps;

class Asserter {
	private $driver;
	private $matcher;

	public function __construct(FileDriverProvider $driver, Matcher $matcher) {
		$this->driver = $driver;
		$this->matcher = $matcher;
	}

	public function forTest(string $testName): TestAsserter {
		return new TestAsserter($testName, $this->driver, $this->matcher);
	}

	public function addSerializer(Serializer $serializer) {
		$this->driver = FileDriver::addSerializer($serializer, $this->driver);
	}
}
