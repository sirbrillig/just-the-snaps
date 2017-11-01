<?php
declare(strict_types=1);

namespace JustSnaps;

class TestAsserter {

	private $testName;
	private $matcher;
	private $driver;

	public function __construct(string $testName, FileDriverProvider $driver, Matcher $matcher) {
		$this->testName = $testName;
		$this->matcher = $matcher;
		$this->driver = $driver;
	}

	public function assertMatchesSnapshot($actual): bool {
		if (! $this->driver->doesSnapshotExistForTest($this->testName)) {
			return false;
		}
		$expected = $this->driver->getSnapshotForTest($this->testName);
		return $this->matcher->doesSnapshotMatch($expected, $actual);
	}
}
