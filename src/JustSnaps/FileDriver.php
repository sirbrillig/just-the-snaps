<?php
declare(strict_types=1);

namespace JustSnaps;

class FileDriver {
	private $cachedData;

	public function __construct(array $data = null) {
		$this->cachedData = $data ?? [];
	}

	public static function buildWithData(array $data): FileDriver {
		return new FileDriver($data);
	}

	public function getSnapshotForTest(string $testName) {
		return $this->cachedData[ $testName ] ?? null;
	}
}
