<?php
declare(strict_types=1);

namespace JustSnaps;

/**
 * Wrapper for a FileDriverProvider to add Serializers
 */
class FileDriverWithSerializer implements FileDriverProvider {
	private $provider;
	private $serializers;

	public function __construct(Serializer $serializer, FileDriverProvider $provider) {
		$this->provider = $provider;
		$this->serializers = [$serializer];
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
		return Serializer::applySerializers($this->serializers, $actual);
	}

	public function addSerializer(Serializer $serializer): void {
		$this->serializers[] = $serializer;
	}
}
