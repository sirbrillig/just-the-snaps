<?php
declare(strict_types=1);

namespace JustSnaps;

/**
 * Snapshot driver that keeps its snapshots on disk
 *
 * The primary driver for snapshots.
 */
class FileDriverDirectory implements FileDriverProvider {
	private $dirName;

	public function __construct(string $dirName) {
		$this->dirName = $dirName;
	}

	public function doesSnapshotExistForTest(string $testName): bool {
		$snapshotFile = $this->getSnapshotFileName($testName);
		if (! file_exists($snapshotFile)) {
			return false;
		}
		return true;
	}

	public function getSnapshotForTest(string $testName): string {
		$snapshotFile = $this->getSnapshotFileName($testName);
		if (! file_exists($snapshotFile)) {
			return null;
		}
		return file_get_contents($snapshotFile);
	}

	public function createSnapshotForTest(string $testName, $actual): void {
		$snapshot = $this->getSnapshotFileName($testName);
		$dirName = dirname($snapshot);
		if (! is_dir($dirName)) {
			mkdir($dirName, 0777, true);
		}
		file_put_contents($snapshot, json_encode($actual));
	}

	public function removeSnapshotForTest(string $testName): void {
		if ($this->doesSnapshotExistForTest($testName)) {
			unlink($this->getSnapshotFileName($testName));
		}
	}

	public function getSnapshotFileName(string $testName): string {
		return $this->dirName . DIRECTORY_SEPARATOR . $testName . '.snap';
	}
}
