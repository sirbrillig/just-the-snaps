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
		$snapshotFile = $this->dirName . DIRECTORY_SEPARATOR . $testName;
		if (! file_exists($snapshotFile)) {
			return null;
		}
		return file_get_contents($snapshotFile);
	}
}
