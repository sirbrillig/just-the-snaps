<?php

namespace JustSnaps;

class Asserter {
	private $driver;
	private $matcher;

	public function __construct(FileDriver $driver, Matcher $matcher) {
		$this->driver = $driver;
		$this->matcher = $matcher;
	}

	public function forTest(string $testName) {
		return new TestAsserter($testName, $this->driver, $this->matcher);
	}
}
