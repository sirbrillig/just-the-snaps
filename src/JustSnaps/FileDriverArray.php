<?php
declare(strict_types=1);

namespace JustSnaps;

/**
 * A snapshot driver that keeps its snapshots in memory
 *
 * Primarily this is for testing, since its snapshots cannot be saved to disk.
 */
class FileDriverArray implements FileDriverProvider {
	private $cachedData;

	public function __construct(array $data = null) {
		$this->cachedData = $data ?? [];
	}

	public function doesSnapshotExistForTest(string $testName): bool {
		return array_key_exists($testName, $this->cachedData);
	}

	public function getSnapshotForTest(string $testName): string {
		return $this->cachedData[ $testName ] ?? null;
	}

	public function createSnapshotForTest(string $testName, $actual): void {
		$this->cachedData[ $testName ] = json_encode($actual);
	}

	public function removeSnapshotForTest(string $testName): void {
		if ($this->doesSnapshotExistForTest($testName)) {
			unset($this->cachedData[ $testName ]);
		}
	}
}
