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
			$this->driver->createSnapshotForTest($this->testName, $actual);
			if ($this->driver instanceof FileDriverReadOnlyWrapper) {
				return false;
			}
			throw new CreatedSnapshotException('Created snapshot for "' . $this->testName . '"; please run the test again.');
		}
		$expected = $this->driver->getSnapshotForTest($this->testName);
		if ($this->driver instanceof FileDriverWithSerializer) {
			return $this->matcher->doesSnapshotMatch($expected, $this->driver->serializeData($actual));
		}
		return $this->matcher->doesSnapshotMatch($expected, $actual);
	}
}
