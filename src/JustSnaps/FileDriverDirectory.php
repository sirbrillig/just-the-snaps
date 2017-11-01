<?php
declare(strict_types=1);

namespace JustSnaps;

class FileDriverDirectory implements FileDriverProvider {
	private $dirName;

	public function __construct(string $dirName) {
		$this->dirName = $dirName;
	}

	public function getSnapshotForTest(string $testName) {
		return $this->cachedData[ $testName ] ?? null;
	}
}
