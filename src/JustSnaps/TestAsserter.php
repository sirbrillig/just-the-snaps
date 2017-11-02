<?php
declare(strict_types=1);

namespace JustSnaps;

/**
 * Performs comparison of a snapshot to actual data
 *
 * Should only be created by Asserter.
 *
 * Each TestAsserter has a name which should be the name of the current test or
 * a hash representing the current test. This name is used as a key to the
 * FileDriverProvider to find or create a snapshot.
 */
class TestAsserter {

	private $testName;
	private $driver;

	/**
	 * Create a new TestAsserter for a test
	 *
	 * @param {string} $testName The name of the test to use as a key for the snapshot
	 */
	public function __construct(string $testName, FileDriverProvider $driver) {
		$this->testName = $testName;
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
			return $this->doesSnapshotMatch($expected, $this->driver->serializeData($actual));
		}
		return $this->doesSnapshotMatch($expected, $actual);
	}

	private function doesSnapshotMatch($original, $actual) {
		return ( $original === json_encode($actual) );
	}
}
