<?php
declare(strict_types=1);

namespace JustSnaps;

class FileDriverDirectory implements FileDriverProvider {
	private $dirName;

	public function __construct(string $dirName) {
		$this->dirName = $dirName;
	}

	public function doesSnapshotExistForTest(string $testName): bool {
		$snapshotFile = $this->dirName . DIRECTORY_SEPARATOR . $testName;
		if (! file_exists($snapshotFile)) {
			return false;
		}
		return true;
	}

	public function getSnapshotForTest(string $testName) {
		$snapshotFile = $this->getSnapshotFileName($testName);
		if (! file_exists($snapshotFile)) {
			return null;
		}
		return file_get_contents($snapshotFile);
	}

	public function createSnapshotForTest(string $testName, $actual) {
		file_put_contents($this->getSnapshotFileName($testName), json_encode($actual));
	}

	private function getSnapshotFileName(string $testName) {
		return $this->dirName . DIRECTORY_SEPARATOR . $testName;
	}
}
