<?php

namespace JustSnaps;

class TestAsserter {

	private $testName;
	private $matcher;
	private $driver;

	public function __construct(string $testName, FileDriver $driver, Matcher $matcher) {
		$this->testName = $testName;
		$this->matcher = $matcher;
		$this->driver = $driver;
	}

	public function assertMatchesSnapshot($actual) {
		$expected = $this->driver->getSnapshotForTest($this->testName);
		return $this->matcher->doesSnapshotMatch($expected, $actual);
	}
}
