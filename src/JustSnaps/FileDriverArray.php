<?php
declare(strict_types=1);

namespace JustSnaps;

class FileDriverArray implements FileDriverProvider {
	private $cachedData;

	public function __construct(array $data = null) {
		$this->cachedData = $data ?? [];
	}

	public function getSnapshotForTest(string $testName) {
		return $this->cachedData[ $testName ] ?? null;
	}
}
