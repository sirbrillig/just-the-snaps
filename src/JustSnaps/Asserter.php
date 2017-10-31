<?php
declare(strict_types=1);

namespace JustSnaps;

class Asserter {
	private $driver;
	private $matcher;

	public function __construct(FileDriver $driver, Matcher $matcher) {
		$this->driver = $driver;
		$this->matcher = $matcher;
	}

	public function forTest(string $testName): TestAsserter {
		return new TestAsserter($testName, $this->driver, $this->matcher);
	}
}
