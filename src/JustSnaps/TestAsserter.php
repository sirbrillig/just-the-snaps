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
	 * @param string $testName The name of the test to use as a key for the snapshot
	 * @param FileDriverProvider $driver The driver to use to get/create snapshots
	 */
	public function __construct(string $testName, FileDriverProvider $driver) {
		$this->testName = $testName;
		$this->driver = $driver;
	}

	/**
	 * Return true if the passed argument matches the snapshot
	 *
	 * The testName of this object is used to determine which snapshot to compare.
	 *
	 * Note that most drivers (except FileDriverReadOnlyWrapper) will create a
	 * snapshot none exists for this testName. In that case, this method will
	 * throw a CreatedSnapshotException (which should mark the test as Skipped).
	 *
	 * @param mixed $actual The data to compare to the snapshot
	 * @return bool True if $actual is the same as its snapshot
	 */
	public function assertMatchesSnapshot($actual): bool {
		if (! $this->driver->doesSnapshotExistForTest($this->testName)) {
			// Note: FileDriverReadOnlyWrapper will make this call a noop
			$this->driver->createSnapshotForTest($this->testName, $actual);
			if ($this->driver instanceof FileDriverReadOnlyWrapper) {
				return false;
			}
			$createMessage = 'Created snapshot for "' . $this->testName . '"; please run the test again.';
			throw new CreatedSnapshotException($createMessage);
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
