<?php
declare(strict_types=1);

namespace JustSnaps;

class FileDriverReadOnlyWrapper implements FileDriverProvider {
	private $provider;

	public function __construct(FileDriverProvider $provider) {
		$this->provider = $provider;
	}

	public function getSnapshotForTest(string $testName): string {
		return $this->provider->getSnapshotForTest($testName);
	}

	public function doesSnapshotExistForTest(string $testName): bool {
		return $this->provider->doesSnapshotExistForTest($testName);
	}

	public function createSnapshotForTest(string $testName, $actual): void {
		$testName;
		$actual;
		// noop
	}

	public function removeSnapshotForTest(string $testName): void {
		$this->provider->removeSnapshotForTest($testName);
	}
}
