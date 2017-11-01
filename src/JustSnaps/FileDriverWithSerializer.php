<?php
declare(strict_types=1);

namespace JustSnaps;

class FileDriverWithSerializer implements FileDriverProvider {
	private $provider;
	private $serializer;

	public function __construct(Serializer $serializer, FileDriverProvider $provider) {
		$this->provider = $provider;
		$this->serializer = $serializer;
	}

	public function getSnapshotForTest(string $testName): string {
		return $this->provider->getSnapshotForTest($testName);
	}

	public function doesSnapshotExistForTest(string $testName): bool {
		return $this->provider->doesSnapshotExistForTest($testName);
	}

	public function createSnapshotForTest(string $testName, $actual): void {
		$this->provider->createSnapshotForTest($testName, $this->serializeData($actual));
	}

	public function removeSnapshotForTest(string $testName): void {
		$this->provider->removeSnapshotForTest($testName);
	}

	public function serializeData($actual) {
		if ($this->serializer->shouldSerialize($actual)) {
			return $this->serializer->serializeData($actual);
		}
		return $actual;
	}
}
